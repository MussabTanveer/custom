import sys
# from nltk.tokenize import word_tokenize
from nltk.tokenize import RegexpTokenizer
from nltk.tag import pos_tag
# from nltk.tag import StanfordPOSTagger
from nltk.corpus import stopwords
from nltk.corpus import wordnet
from nltk.stem import WordNetLemmatizer
# from nltk.stem import SnowballStemmer
import verbs


def preprocess_question(question):
    # Question Pre-processing
    question = question.lower()  # question to lower case
    print("Input Question: " + question)
    tokenizer = RegexpTokenizer(r'\w+')  # punctuation removal and tokenize words
    tokens = tokenizer.tokenize(question)
    # tokens = word_tokenize(question)  # tokenize question words
    # print("<br><br><b>Tokens:</b> " + str(tokens))
    stop_words = set(stopwords.words("english"))
    filtered_tokens = [w for w in tokens if not w in stop_words]  # stop words removal
    # print("<br><br><b>Filtered Tokens:</b> " + str(filtered_tokens))
    # ss = SnowballStemmer("english")
    # stemmed_tokens = [ss.stem(w) for w in filtered_tokens]  # stemming
    # print(stemmed_tokens)
    # lemmatizer = WordNetLemmatizer()
    # lemmatized_tokens = [lemmatizer.lemmatize(w, pos="v") for w in filtered_tokens]  # lemmatizing
    # print(lemmatized_tokens)
    # tagged = pos_tag(tokens)  # pos tag words
    tagged = pos_tag(filtered_tokens)  # pos tag filtered words
    # print("<br><br><b>POS Tagged Question:</b> " + str(tagged))
    return tagged


def find_level(verb):
    count = 0
    levels = []
    for b in verbs.bloom:
        if verb.lower() in b:
            levels.append(verbs.level[str(count)])
        count = count + 1
    if levels:
        return levels
    return "Unknown"  # if verb is not in verb list


def print_level(verb, level):
    if level != "Unknown":
        print("Your Question Has Been Classified!")
    print("Extracted Verb: " + verb.title())
    print("Bloom's Taxonomy Level(s): ")
    for l in level:
        print(str(l))


def classify(tagged):
    for t in tagged:
        if t[1] == 'VB':
            verb = t[0]
            level = find_level(t[0])
            if level == "Unknown":
                continue
            print_level(verb, level)
            break


def input_question(question):
    tagged = preprocess_question(question)  # pre-process question
    classify(tagged)  # classify question level

Question = ""
for q in sys.argv[1:]:
    Question += " "
    Question += q

input_question(Question)
