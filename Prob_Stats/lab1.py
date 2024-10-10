import numpy as np
import matplotlib.pyplot as plt

# Choose number of chords to draw in the simulation:
num_chords = 10000


def draw_circle_and_triangle(ax):
    circle = plt.Circle((0, 0), 1, color='w', linewidth=2, fill=False)
    ax.add_patch(circle)  # Draw circle
    ax.plot([np.cos(np.pi / 2), np.cos(7 * np.pi / 6)],
            [np.sin(np.pi / 2), np.sin(7 * np.pi / 6)], linewidth=2, color='g')
    ax.plot([np.cos(np.pi / 2), np.cos(- np.pi / 6)],
            [np.sin(np.pi / 2), np.sin(- np.pi / 6)], linewidth=2, color='g')
    ax.plot([np.cos(- np.pi / 6), np.cos(7 * np.pi / 6)],
            [np.sin(- np.pi / 6), np.sin(7 * np.pi / 6)], linewidth=2, color='g')
    plt.show()


def bertrand_simulation(method_number):
    # Simulation initialisation parameters
    count = 0

    # Figure initialisation
    plt.style.use('dark_background')  # use dark background
    ax = plt.gca()
    ax.cla()  # clear things for fresh plot
    ax.set_aspect('equal', 'box')
    ax.set_xlim((-1, 1))  # Set x axis limits
    ax.set_ylim((-1, 1))  # Set y axis limits
    
    # Repeat the following simulation num_chords times:

    # Step 1: Construct chord coordinates according to chosen method

    # Step 2: Compute length of chord and compare it with triangle side
    # Display probability after each simulation
    # Plot the first 1000 chords
    # Hint: Use different colors for chords longer than the triangle side
    # and make the chords more transparent by setting alpha = 0.1
    for _ in range(10):
        x, y = bertrand_methods[method_number]()
        length = np.sqrt((x[0] - x[1])**2 + (y[0] - y[1])**2)
        if length > np.sqrt(3):
            ax.plot(x, y, color='r', alpha=0.5)
            count += 1
        else:
            ax.plot(x, y, color='b', alpha=0.5)

    print(f'Probability of a chord longer than the side of the triangle: {count / num_chords}')
    draw_circle_and_triangle(plt.gca())
    plt.show()


def bertrand1():
    theta1 = np.random.rand() * 2 * np.pi
    theta2 = np.random.rand() * 2 * np.pi 

    x = [np.cos(theta1), np.cos(theta2)]
    y = [np.sin(theta1), np.sin(theta2)]

    return x, y

def bertrand2():
    theta = np.random.rand() * 2 * np.pi
    r = np.random.rand()
    x0, y0 = r * np.cos(theta), r * np.sin(theta)
    theta1 = np.arccos(r)
    x = [np.cos(theta1 + theta), np.cos(theta1 - theta)]
    y = [np.sin(theta1 + theta), np.sin(theta1 - theta)]

    return x, y

bertrand_methods = {1: bertrand1, 2 : bertrand2}

# method_choice = input('Choose method to simulate: ')
bertrand_simulation(1)