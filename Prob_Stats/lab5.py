import numpy as np

def verif3(nr_throws):
    v = np.random.randint(0, 2, nr_throws)


def test(nr_throws, nr_experiments):
    nr_succes = 0
    for _ in range(nr_experiments):
        nr_succes += verif3(nr_throws)
    print(nr_succes / nr_experiments)

test(100, 10000)

