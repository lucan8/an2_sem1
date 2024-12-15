import numpy as np
import matplotlib.pyplot as plt

def testExpVar(nr_tests, _lambda):
    probs = np.random.random(nr_tests)
    weights = -1 / _lambda * np.log(probs)
    return weights, probs

def testCauchyVar(nr_tests, x0, gama):
    probs = np.random.random(nr_tests)
    weights = x0 + gama * np.tan(np.pi * (probs - 1 / 2))
    return weights, probs

def drawExpVar():
    _lambda = 1
    nr_tests = 100000
    weights, probs = testExpVar(nr_tests, _lambda)
    max_weight = max(weights)

    xs = np.linspace(start=0, stop=max_weight, num=50)
    ys = np.exp(xs * (-_lambda))

    plt.hist(weights, bins = 50, color = ['red'], ec = 'black', density=True)
    plt.plot(xs, ys)
    plt.show()

def drawCauchyVar():
    x0 = 0
    gama = 1

    nr_tests = 100000
    weights, probs = testCauchyVar(nr_tests, x0, gama)
    min_weight = min(weights)
    max_weight = max(weights)

    # xs = np.linspace(start=min_weight, stop=max_weight, num=50)
    # ys = 1 / ((np.pi * gama) * (1 + (xs - x0 / gama) ** 2))

    plt.hist(weights, bins=100, range=(-10, 10), color = ['red'], ec = 'black', density=True)
    # plt.plot(xs, ys)
    plt.show()

    #print(np.mean([np.mean(weights) for i in range(nr_tests)]))

drawCauchyVar()



