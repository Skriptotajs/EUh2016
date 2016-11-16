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


with open('en.corpus.json', 'r') as data_file:
    corpus = json.load(data_file)

i = 0
with open("en.txt", "r") as f:
    for line in f:
        if i < len(corpus):
            i += 1
            continue
        try:
            corpus.append(analyse(line))
        except Exception as e:
            print(i)
            print(e.message)
            corpus.append([])

        i += 1
        if (i % 1000) == 0:
            print(i)
            dic = corpora.Dictionary(corpus, prune_at=200000)

            bows = []
            for doc in corpus:
                bows.append(dic.doc2bow(doc))

            tfidf = models.TfidfModel(bows)

            dic.save("en.dic")
            tfidf.save("en.tfidf")

            with open('en.corpus.json', 'w') as data_file:
                json.dump(corpus, data_file, indent=4)

print(i)
dic = corpora.Dictionary(corpus, prune_at=None)
dic.filter_extremes(no_below=0, no_above=0.03, keep_n=None)

bows = []
for doc in corpus:
    bows.append(dic.doc2bow(doc))

tfidf = models.TfidfModel(bows)

dic.save("en.dic")
tfidf.save("en.tfidf")

with open('en.corpus.json', 'w') as data_file:
    json.dump(corpus, data_file, indent=4)
