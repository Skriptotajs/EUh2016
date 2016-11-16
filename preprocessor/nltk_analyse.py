# coding=utf-8
import httplib, urllib, base64
from pprint import pprint
import sys
import json
import time
import itertools, nltk, string
from gensim import corpora, models, utils


reload(sys)
sys.setdefaultencoding('utf-8')

nltk.download('punkt')
nltk.download('averaged_perceptron_tagger')

def analyse(text, grammar=r'KT: {(<JJ>* <NN.*>+ <IN>)? <JJ>* <NN.*>+}'):


    # exclude candidates that are stop words or entirely punctuation
    punct = set(string.punctuation)
    stop_words = set(nltk.corpus.stopwords.words('english'))
    # tokenize, POS-tag, and chunk using regular expressions
    chunker = nltk.chunk.regexp.RegexpParser(grammar)
    tagged_sents = nltk.pos_tag_sents(nltk.word_tokenize(sent) for sent in nltk.sent_tokenize(text))
    all_chunks = list(itertools.chain.from_iterable(nltk.chunk.tree2conlltags(chunker.parse(tagged_sent))
                                                    for tagged_sent in tagged_sents))
    # join constituent chunk words into a single chunked phrase
    candidates = [' '.join(word for word, pos, chunk in group).lower()
                  for key, group in itertools.groupby(all_chunks, lambda (word, pos, chunk): chunk != 'O') if key]

    return [cand for cand in candidates
            if cand not in stop_words and not all(char in punct for char in cand)]

dic = corpora.Dictionary.load("en.dic")

with open('../crawl/matches.json') as data_file:
    data = json.load(data_file)

# create groups
groups = {}
for doc in data:
    group_id = doc['date'][:4]
    if group_id not in groups:
        groups[group_id] = {
            'size': 0,
            'bow': {}
        }

    bow = dic.doc2bow(analyse(doc['text']))
    groups[group_id]['size'] += 1
    for item in bow:
        id = item[0]

        if id not in groups[group_id]['bow']:
            groups[group_id]['bow'][id] = 0

        groups[group_id]['bow'][id] += 1

# get most relevant globally

tfidf = models.tfidfmodel.TfidfModel.load('en.tfidf')
limit = 20
for key in groups:
    top_bow_weighted = sorted(tfidf[groups[key]['bow'].items()], key=lambda x: x[1], reverse=True)[:limit]

    top_bow = []
    for item in top_bow_weighted:
        top_bow.append((item[0], groups[key]['bow'][item[0]]))

    groups[key]['top_bow'] = top_bow

    tokens = []
    for item in top_bow:
        tokens.append((dic[item[0]], item[1]))
    groups[key]['top_phrases'] = tokens


# create local tfidf model from top_bow
tfidf = models.tfidfmodel.TfidfModel([groups[item]['top_bow'] for item in groups])

for key in groups:

    top_bow_weighted = sorted(tfidf[groups[key]['top_bow']], key=lambda x: x[1], reverse=True)

    tokens = []
    for item in top_bow_weighted:
        tokens.append((dic[item[0]], item[1]))

    groups[key]['top_phrases_reweighed'] = tokens

with open('groups.json', 'w') as data_file:
    json.dump(groups, data_file, indent=4)
