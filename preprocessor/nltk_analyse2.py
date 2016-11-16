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

# nltk.download('punkt')
# nltk.download('averaged_perceptron_tagger')

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

# with open('../crawl/matches_new.json') as data_file:
#     data = json.load(data_file)
#
# for doc in data:
#     doc['topics'] = analyse(doc['text'])
#
# with open('matches_new.json', 'w') as data_file:
#     json.dump(data, data_file, indent=4)

with open('matches_new.json') as data_file:
    data = json.load(data_file)


for doc in data:
    doc['bow'] = dic.doc2bow(doc['topics'])


tfidf_global = models.tfidfmodel.TfidfModel.load('en.tfidf')
tfidf_local = models.tfidfmodel.TfidfModel([doc['bow'] for doc in data])

bad_ids = []
for i in tfidf_local.dfs:
    if tfidf_local.dfs[i] > 100:
        bad_ids.append(i)

dic.filter_tokens(bad_ids=bad_ids)
for doc in data:
    doc['bow'] = dic.doc2bow(doc['topics'])

tfidf_local = models.tfidfmodel.TfidfModel([doc['bow'] for doc in data])

# freq = []
# for i in tfidf_local.dfs:
#     freq.append((dic[i], tfidf_local.dfs[i]))
# freq = sorted(freq, key=lambda x: x[1], reverse=True)
# with open('freq.json', 'w') as data_file:
#     json.dump(freq, data_file, indent=4)

# create groups
groups = {}
for doc in data:
    # group_id = doc['date'][:7]
    group_id = doc['country']
    if group_id not in groups:
        groups[group_id] = {
            'size': 0,
            'bow': {}
        }

    groups[group_id]['size'] += 1
    for item in doc['bow']:
        id = item[0]

        if id not in groups[group_id]['bow']:
            groups[group_id]['bow'][id] = 0

        groups[group_id]['bow'][id] += 1

for key in groups:
    bow = groups[key]['bow'].items()
    bow_local = tfidf_local[bow]
    bow_global = tfidf_global[bow]

    bow_merged = []
    for i in range(0, len(bow_local)):
        average = bow_local[i][1]
        #average = bow_local[i][1] * bow_global[i][1]
        #average = 2 * bow_local[i][1] * bow_global[i][1] / (bow_local[i][1] + bow_global[i][1])
        #average = bow_local[i][1] * 0.3 + bow_global[i][1] * 0.7#
        bow_merged.append((bow_global[i][0], average, bow_local[i][1], bow_global[i][1], bow[i][1]))

    top_bow = sorted(bow_merged, key=lambda x: (x[2], x[3]), reverse=True)

    tokens = []
    for item in top_bow:
        tokens.append({
            'token': dic[item[0]],
            'local': item[2],
            'global': item[3],
            'count': item[4]
        })
    groups[key]['top_phrases'] = tokens

    groups[key]['name'] = key

    del groups[key]['bow']

groups = sorted(groups.values(), key=lambda x: x['size'], reverse=True)

with open('groups_country.json', 'w') as data_file:
    json.dump(groups, data_file, indent=4)
