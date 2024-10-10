def arrangements(nr_values, nr_ways):
    return factorial(nr_values) / factorial(nr_values - nr_ways)

def combinations(nr_values, nr_ways):
    return arrangements(nr_values, nr_ways) / factorial(nr_ways)


def factorial(n):
    res = 1
    for i in range(n):
        res *= i + 1
    return res

def nrPasswords(pass_size = 8, letter_big = True):
    nr_letter_small = 26
    nr_digits = 10
    nr_letter_big = 0

    if letter_big:
        nr_letter_big = 26
        
    return (nr_letter_small + nr_letter_big + nr_digits) ** pass_size

def probDistinctPass(start_digit = False, pass_size = 8, letter_big = True):
    nr_letter_small = 26
    nr_digits = 10
    nr_letter_big = 0

    if letter_big:
        nr_letter_big = 26
    
    nr_chars = nr_letter_big + nr_letter_small + nr_digits
    nr_letters = nr_letter_big + nr_letter_small
    
    if not start_digit:
        return str(arrangements(nr_chars, pass_size) / nrPasswords(pass_size, letter_big) * 100) + '%'
    return str(nr_letters * arrangements(nr_chars - 1, pass_size - 1) / nrPasswords(pass_size, letter_big) * 100) + '%'

#tries/sec
def nrYears(computation_speed = 10 ** 6, pass_size = 8):
   return nrPasswords(pass_size) / (computation_speed * 3600 * 24 * 365)

def weekProbability(computation_speed = 10 ** 6, pass_size = 8):
    week_in_sec = 7 * 24 * 3600
    nr_pass_tried = computation_speed * week_in_sec / nrPasswords(pass_size)
    return str(nr_pass_tried * 100) + '%'

def probabilityBuyCrappyLaptop(bought_laptops = 6, bought_crappy = 3, nr_laptops = 20, nr_crappy = 7):
    nr_good = nr_laptops - nr_crappy
    bought_good = bought_laptops - bought_crappy

    crappy_combinations = combinations(nr_crappy, bought_crappy)
    good_combinations = combinations(nr_good, bought_good)
    all_combinations = combinations(nr_laptops, bought_laptops)

    return crappy_combinations * good_combinations / all_combinations * 100

def mostProbableCrappyLaptopCount(nr_laptops = 20, nr_crappy = 7):
    for i in range(1, nr_laptops + 1):
        prob_dist = [tuple([j, probabilityBuyCrappyLaptop(i, j, nr_laptops, nr_crappy)]) for j in range(0, min(i + 1, nr_crappy + 1))]
        most_probale = max(prob_dist, key=lambda x: x[1])
        print(f"For {i} laptops most probable crappy count is {most_probale[0]} with a probability of {most_probale[1]}%")
            

def probabilityAceExtraction(nr_aces = 3, nr_cards_extracted = 5):
    ace_comb = combinations(4, nr_aces)
    remaining_cards_combinations = combinations(52 - nr_aces, nr_cards_extracted - nr_aces)
    all_combinations = combinations(52, nr_cards_extracted)
    return ace_comb * remaining_cards_combinations / all_combinations * 100

def mostProbableAceExtraction(nr_cards_extracted = 5):
    most_prob = max([tuple([i, probabilityAceExtraction(i, nr_cards_extracted)]) for i in range(1, 5)],
                    key= lambda x: x[1])
    print(f"For {nr_cards_extracted} cards extraction, most probable nr of aces extracted: {most_prob[0]}, with a probability of {most_prob[1]}%")


def main():
    print(f"NR PASSWORDS: {nrPasswords()}")
    print(f"NR OF YEARS: {nrYears()}")
    print(f"WEEK PROBABILITY: {weekProbability()}")
    print(f"PROBABILITY DISTINCT PASSWORD: {probDistinctPass()}")
    print(f"PROBABILITY DISTINCT PASSWORD NO START DIGIT: {probDistinctPass(True)}")
    print(f"NR FOLDERS COMBINATIONS: {combinations(10, 3)}")
    print(f"NR 3 CRAPPY: {str(probabilityBuyCrappyLaptop()) + '%'}")
    mostProbableCrappyLaptopCount()
    print(f"EXTRACT 5 CARDS, PROBABILITY OF 3 ACES: {str(probabilityAceExtraction()) + '%'}")
    mostProbableAceExtraction()

main()
