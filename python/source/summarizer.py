# -*- coding: utf-8 -*-
from __future__ import absolute_import
from __future__ import division, print_function, unicode_literals

import sys

from sumy.parsers.plaintext import PlaintextParser
from sumy.nlp.tokenizers import Tokenizer
from sumy.summarizers.lsa import LsaSummarizer as Summarizer
from sumy.nlp.stemmers import Stemmer
from sumy.utils import get_stop_words, parse_stop_words

from data.english import englishStopWords

LANGUAGE = "english"

if __name__ == '__main__':
    text = sys.argv[1]
    maxSentences = int(sys.argv[2])

    parser = PlaintextParser.from_string(text, Tokenizer(LANGUAGE))
    stemmer = Stemmer(LANGUAGE)
    summarizer = Summarizer(stemmer)
    summarizer.stop_words = parse_stop_words(englishStopWords)

    for sentence in summarizer(parser.document, maxSentences):
        print(sentence, "\n")
