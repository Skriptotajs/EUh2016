from __future__ import division
from gensim import corpora, models, utils
from pprint import pprint
import json

with open('phrases.json') as data_file:
    data = json.load(data_file)

corpus = []
for item in data:
    if (item['text'].find("Madam President") > -1 or item['text'].find("Mr President") > -1) and 'key_phrases' in item and item['key_phrases'] is not None:
        corpus.append(item['key_phrases'])
    else:
        corpus.append([])

dic = corpora.Dictionary(corpus, prune_at=None)

bows = []
for doc in corpus:
    bows.append(dic.doc2bow(doc))

tfidf = models.TfidfModel(bows)

for i, bow in enumerate(bows):
    data[i]['bow_weighted'] = bow

# data = sorted(data, key=lambda x: x.date)
# for doc in data[:20]:
#     top_bow = sorted(tfidf[doc['bow_weighted']], key=lambda x: x[1], reverse=True)[:10]
#     tokens = []
#     for item in top_bow:
#         tokens.append((dic[item[0]], item[1]))
#
#     print(doc['text'])
#     pprint(tokens)
#
# exit()

grouped = {}
for doc in data:
    # group_id = doc['date'][:4]
    group_id = doc['country']
    if group_id not in grouped:
        grouped[group_id] = {}

    for item in doc['bow_weighted']:
        id = item[0]

        if id not in grouped[group_id]:
            grouped[group_id][id] = 0

        grouped[group_id][id] += 1

group_corpus = []
for key in grouped:
    group_corpus.append(grouped[key].items())



tfidf = models.TfidfModel(group_corpus)

for key in grouped:
    items = grouped[key].items()
    items = tfidf[items]
    grouped[key] = sorted(items, key=lambda x: x[1], reverse=True)[:30]

    tokens = []
    for item in grouped[key]:
        tokens.append((dic[item[0]], item[1]))

    grouped[key] = tokens

pprint(grouped)
