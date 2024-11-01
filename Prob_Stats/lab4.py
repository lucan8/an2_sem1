import numpy as np

nr_experiments = 1000
   
def test_albaNeagra(nr_doors, nr_open):
    changes_victory = 0
    remains_victory = 0

    for _ in range(nr_experiments):
        options = list(range(1, nr_doors + 1))
        correct_choice = np.random.randint(1, nr_doors + 1)
        user_choice = np.random.randint(1, nr_doors + 1)

        if user_choice == correct_choice:
            remains_victory += 1
        else:
            options.remove(user_choice)
            options.remove(correct_choice)

            for _ in range(nr_open):
                options.remove(np.random.choice(options))

            options.append(correct_choice)

            user_choice = np.random.choice(options)
            if user_choice == correct_choice:
                changes_victory += 1

    print(f"Remains: {remains_victory / nr_experiments}")
    print(f"Changes: {changes_victory / nr_experiments}")

test_albaNeagra(3, 1)