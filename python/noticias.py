import json
import sys
import spacy
from newsapi import NewsApiClient
from neo4j import GraphDatabase

# API key as an environment variable for security
import os

class Neo4jHandler:
    def __init__(self, uri, user, password):
        self.driver = GraphDatabase.driver(uri, auth=(user, password))

    def close(self):
        self.driver.close()

    def create_node(self, tx, label, properties):
        tx.run(f"CREATE (n:{label} $properties)", properties=properties)

    def create_relationship(self, tx, node1, node2, relationship):
        tx.run(f"MATCH (a),(b) WHERE a.name = $node1 AND b.name = $node2 "
               f"CREATE (a)-[r:{relationship}]->(b)", node1=node1, node2=node2)

def fetch_news(api_key, query, language='es', sort_by='relevancy'):
    newsapi = NewsApiClient(api_key=api_key)
    try:
        all_articles = newsapi.get_everything(q=query, language=language, sort_by=sort_by)
        return all_articles['articles']
    except Exception as e:
        print(f"Error fetching news: {e}")
        return []

def preprocess_text(article, nlp):
    text = f"{article['title']}. {article['description']}. {article.get('content', '')}"
    doc = nlp(text)
    tokens = [token.lemma_ for token in doc if not token.is_stop and token.is_alpha]
    return ' '.join(tokens)

def extract_entities_and_relations(doc):
    entities = [(ent.text, ent.label_) for ent in doc.ents]
    relations = []

    for token in doc:
        if token.dep_ in ("nsubj", "dobj", "iobj", "pobj"):
            subject = [w for w in token.head.lefts if w.dep_ in ("det", "nsubj", "nmod")]
            if subject:
                relation = {
                    'head': token.head.text,
                    'relation': token.dep_,
                    'subject': subject[0].text,
                    'object': token.text
                }
                relations.append(relation)

    return entities, relations

def process_single_article(nlp, article):
    text = preprocess_text(article, nlp)
    doc = nlp(text)
    entities, relations = extract_entities_and_relations(doc)
    return entities, relations

def remove_duplicates(items):
    seen = []
    for item in items:
        if item not in seen:
            seen.append(item)
    return seen

def main(api_key, query, neo4j_uri, neo4j_user, neo4j_password):
    nlp = spacy.load('es_core_news_md')
    articles = fetch_news(api_key, query)
    all_entities = []
    all_relations = []
    neo4j_handler = Neo4jHandler(neo4j_uri, neo4j_user, neo4j_password)
    with neo4j_handler.driver.session() as session:
        for article in articles:
            entities, relations = process_single_article(nlp, article)
            all_entities.extend(entities)
            all_relations.extend(relations)
            for entity in entities:
                session.write_transaction(neo4j_handler.create_node, entity[1], {"name": entity[0]})
            for relation in relations:
                session.write_transaction(neo4j_handler.create_relationship, relation['subject'], relation['object'], relation['relation'])

    neo4j_handler.close()
    data = {
        'entities': remove_duplicates(all_entities),
        'relations': remove_duplicates(all_relations)
    }
    json_data = json.dumps(data)
    return json_data

if __name__ == "__main__":
    if len(sys.argv) != 6:
        print("Uso: python noticias_spacy.py <API_KEY> <query> <neo4j_uri> <neo4j_user> <neo4j_password>")
        sys.exit(1)
    api_key = sys.argv[1]
    query = sys.argv[2]
    neo4j_uri = sys.argv[3]
    neo4j_user = sys.argv[4]
    neo4j_password = sys.argv[5]
    json_data = main(api_key, query, neo4j_uri, neo4j_user, neo4j_password)
    print(json_data)
