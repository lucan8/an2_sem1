import numpy as np
import matplotlib.pyplot as plt
import multiprocessing
import time
import os

def main():
    start = time.time()
    test()
    print("Single thread: ", time.time() - start)

    start = time.time()
    testMulti()
    print("Multi thread: ", time.time() - start)


def test():
    #Coin
    drawCoinGraph([0], [0, 1])
    #Bad Coin
    drawCoinGraph([0, 1, 2], [0, 1, 2, 3])
    #Dice
    drawDiceGraph()

def testMulti():
    curr_proc = os.getpid()
    #Coin
    p1 = multiprocessing.Process(target=drawGraph, args=([0], [0, 1]))
    #Bad Coin
    p2 = multiprocessing.Process(target=drawGraph, args=([0, 1, 2], [0, 1, 2, 3]))
    #Dice
    p3 = multiprocessing.Process(target=drawDiceGraph)
    if curr_proc == os.getpid():
        p1.start()
    if curr_proc == os.getpid():
        p2.start()
    if curr_proc == os.getpid():
        p3.start()

    if curr_proc == os.getpid():
        p1.join()
    if curr_proc == os.getpid():
        p2.join()
    if curr_proc == os.getpid():
        p3.join()

def drawGraph(chosen_options, all_options):
    ypoints = []
    xpoints = range(1, 7)
    
    np.random.seed(int(time.time()))
    #multiprocessing.Pool().map(lambda power: ypoints.append(sum(1 for elem in np.random.randint(0, len(all_options), power) if elem in chosen_options) / power), [10 ** i for i in xpoints])
    ypoints = [sum(1 for elem in np.random.randint(0, len(all_options), power) if elem in chosen_options) / power for i in xpoints if (power := 10 ** i)]
    
    plt.xlabel("Number Simulations")
    plt.ylabel("Probabilities")
    plt.plot(xpoints, ypoints)

def drawDiceGraph():
    options = list(range(0, 6))
    drawGraph([0], options)
    drawGraph([1], options)
    drawGraph([2], options)
    drawGraph([3], options)
    drawGraph([4], options)
    drawGraph([5], options)
    plt.show()

def drawCoinGraph(chosen_options, all_options):
    drawGraph(chosen_options, all_options)
    plt.show()

main()


