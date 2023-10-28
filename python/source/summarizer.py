# -*- coding: utf-8 -*-
from __future__ import absolute_import
from __future__ import division, print_function, unicode_literals

import re
import sys

from sumy.models.dom import Sentence
from sumy.nlp.stemmers import Stemmer
from sumy.nlp.tokenizers import Tokenizer
from sumy.parsers.plaintext import PlaintextParser
from sumy.summarizers.lsa import LsaSummarizer as Summarizer
from sumy.utils import parse_stop_words

from data.english import englishStopWords

LANGUAGE = "english"

elided_msg = "**(tldr: {} sentences skipped)**  "


class DiscloseElided_LsaSummarizer_Mixin(object):
    @staticmethod
    def _get_best_sentences(sentences, count, rate):
        top_n = sorted(range(len(sentences)), key=rate, reverse=True)[:count]

        elided = 0

        for i, s in enumerate(sentences):
            if i in top_n:
                if elided:
                    yield elided_msg.format(elided)
                    elided = 0
                yield s
            else:
                elided += 1
        if elided:
            yield elided_msg.format(elided)


class Summarizer(DiscloseElided_LsaSummarizer_Mixin, Summarizer):
    pass


if __name__ == '__main__':
    text = sys.argv[1]
    maxSentences = int(sys.argv[2])

    parser = PlaintextParser.from_string(text, Tokenizer(LANGUAGE))
    stemmer = Stemmer(LANGUAGE)
    summarizer = Summarizer(stemmer)
    summarizer.stop_words = parse_stop_words(englishStopWords)

    checkPattern = re.compile(r'\*\*\(tldr: \d+ sentences skipped\)\*\* {2}')

    sentence: Sentence
    for sentence in summarizer(parser.document, maxSentences):
        if checkPattern.match(str(sentence)):
            print(sentence)
        else:
            print(sentence, "\n")
