import numpy as np
import matplotlib.pyplot as plt

def Bernoulli(p, nr_tests):
    tests = np.random.random(nr_tests) < p
    return tests

def binomialHist():
    p = np.random.rand()
    nr_tests = 10

    tests = [sum(Bernoulli(p, nr_tests) for i in range(nr_tests))]
    bins = [i - 0.5 for i in range(nr_tests + 2)]

    plt.hist(tests, bins = bins, color = ['red'], ec = 'black', density=True)
    plt.title("Binomial with p = " + str(round(p, 2)) + "\n nr_tests = " + str(nr_tests))
    plt.xticks(range(nr_tests))
    plt.show()

def bernoulliHist():
    p = np.random.rand()
    nr_tests = 1000

    tests = Bernoulli(p, nr_tests)
    bins = [-0.5, 0.5, 1.5]
    #counts = np.histogram(tests, bins = bins)
    plt.hist(tests, bins = bins, color = ['red'], ec = 'black', density=True)
    plt.title("Bernoulli with p = " + str(round(p, 2)) + " and nr_tests = " + str(nr_tests))
    plt.xticks([0, 1])
    plt.show()

def geometricHist():
    p = np.random.rand()
    nr_tests = 10000

    tests = [firstTrue(p) for _ in range(nr_tests)]
    max_test = max(tests)
    bins = [i - 0.5 for i in range(max_test + 2)]
    #counts = np.histogram(tests, bins = bins)
    plt.hist(tests, bins = bins, color = ['red'], ec = 'black', density=True)
    plt.title("Geometric with p = " + str(round(p, 2)) + " and nr_tests = " + str(nr_tests))
    plt.xticks(range(max_test))
    plt.show()

def firstTrue(p):
    first_true = 0
    while True:
        res = Bernoulli(p, 1)
        if res[0]:
            return first_true
        first_true += 1
        
#bernoulliHist()
#binomialHist()
#geometricHist()

