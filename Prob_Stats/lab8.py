import numpy as np
import concurrent.futures

def testPlays(initial_sum, nr_plays, bet_percentage):
    return initial_sum * np.prod(np.random.choice(a = [1 / 2, 3 / 2, 21 / 20], size = nr_plays,
                                 p = [1 / 6, 1 / 6, 4 / 6]) * bet_percentage + (1 - bet_percentage))

def getArithmeticMean(nr_plays, initial_sum, bet_percentage, nr_tests):
    return np.mean([testPlays(initial_sum, nr_plays, bet_percentage) for _ in range(nr_tests)])

def main():
    nr_tests = 10 ** 5
    initial_sum = 1
    bet_percentage = 0.4
    nr_plays = 300
    nr_means = 10
    
    executor = concurrent.futures.ThreadPoolExecutor()
    threads = [executor.submit(getArithmeticMean, nr_plays, initial_sum, bet_percentage, nr_tests)
                for _ in range(nr_means)]
    results = [thread.result() for thread in threads]
    print(np.median(results))
    print(np.median([testPlays(initial_sum, nr_plays, bet_percentage) for _ in range(nr_tests)]))

main()

