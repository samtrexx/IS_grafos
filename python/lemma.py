import spacy
import sys
import json

# Cargar el modelo de spaCy para español
nlp = spacy.load("es_core_news_sm")

def lemmatize(text):
    doc = nlp(text)
    lemmas = [token.lemma_ for token in doc]
    return lemmas

if __name__ == "__main__":
    input_text = sys.argv[1]
    lemmas = lemmatize(input_text)
    print(json.dumps(lemmas))
