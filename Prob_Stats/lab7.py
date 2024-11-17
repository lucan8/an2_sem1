import numpy as np
import matplotlib.pyplot as plt
def BernoulliMedie(var_aleatoare, offset):
    weights = np.array(var_aleatoare[0]) + offset
    probs = var_aleatoare[1]
    return np.prod(np.power(weights, probs))

def getInvestingPercent(var_aleatoare, starting_sum):
    start = 0
    end = 1
    precision = 1

    while True:
        mid = (start + end) / 2
        res = BernoulliMedie(var_aleatoare, (1 - mid) * starting_sum)
        diff = res - starting_sum
        if abs(diff) < precision:
            return mid
        elif diff > 0:
            start = mid
        else:
            end = mid

def plotAlfas(var_aleatoare, starting_sum):
    start = 0
    end = 1

    results = []
    alfas = []

    for _ in range(1, 100):
        mid = (start + end) / 2
        res = BernoulliMedie(var_aleatoare, (1 - mid) * starting_sum)

        results.append(res)
        alfas.append(mid)

        if res > starting_sum:
            start = mid
        else:
            end = mid

        
    plt.plot(alfas, results)
    plt.title("Initial sum: " + str(starting_sum))
    plt.xlabel("alfas")
    plt.ylabel("sums")
    plt.show()

v = ([1, 2, 6, 22, 200, 1_000_000], [1/6] * 6)
w = 100_000

plotAlfas(v, w)

res = getInvestingPercent(v, w)
print(res)



