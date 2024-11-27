import matplotlib.pyplot as plt
import numpy as np
def kProb(k, n):
    return k / n * sum([1 / i for i in range(k, n)])

list_n = [10 ** i for i in range(1, 6)]
theory = [int(n) / np.e for n in list_n]
probs = [max([(kProb(k, n), k, n) for k in range(1, n - 1)]) for n in list_n]

plt.plot(list_n, [prob[1] for prob in probs])
plt.plot(list_n, theory)
plt.show()
