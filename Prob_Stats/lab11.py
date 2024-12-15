import numpy as np
import matplotlib.pyplot as plt
from scipy.integrate import quad # De comparat cu integrarea MC


def needle_dropping(num_drops=100000, needle_length=1, line_spacing=2):
    def setup_figure(ax):
        ax.cla()  # clear canvas for fresh plot
        ax.set_aspect('equal', 'box')
        ax.set_xlim((-2-needle_length / 2, 2+needle_length / 2)) 
        ax.set_ylim((-(line_spacing + needle_length) / 2, 
                      (line_spacing + needle_length) / 2))


    def draw_line(ax):
        ax.plot([-2 - needle_length / 2, 2 + needle_length / 2], 
                [line_spacing / 2, line_spacing / 2], linewidth=2, color='g')
        ax.plot([-2 - needle_length / 2, 2 + needle_length / 2], 
                [- line_spacing / 2, - line_spacing / 2], linewidth=2, color='g')
        
        
    def draw_needle(center_x, center_y, angle, correct):
        x = [center_x - 0.5 * needle_length * np.cos(angle), 
             center_x + 0.5 * needle_length * np.cos(angle)]
        y = [center_y - 0.5 * needle_length * np.sin(angle), 
             center_y + 0.5 * needle_length * np.sin(angle)]
        if correct:
            plt.plot(x, y, color='blue')
        else:
            plt.plot(x, y, color='red')
        
    # Figure initialisation
    setup_figure(plt.gca())
    draw_line(plt.gca())
    
    # Simulate num_drops needles and compute the empirical probability of
    # crossing the line whilst drawing the needles
    def generateNeedles():
        center_x = (np.random.random(num_drops) * 4) - 2
        center_y = (np.random.random(num_drops) * line_spacing) - line_spacing / 2
        omega = np.random.random(num_drops) * np.pi


        d = np.sin(omega) * needle_length / 2
        y_down_edge, y_up_edge = center_y - d, center_y + d

        results = (y_down_edge <= -line_spacing / 2) | (y_up_edge >= line_spacing / 2)
        prob = sum(results) / len(results)
        for args in zip(center_x, center_y, omega, results):
            draw_needle(*args)
        plt.show()
        return prob
    
    print(generateNeedles(), (2 * needle_length) / (np.pi * line_spacing))  
    # Compare the theoretical and the empirical probabities
    # ...
    
    # Approximate pi using the simulation
    # ...
    
    
def MC_integral(f, num_sim=100, TOL=0.01, trust = 0.95):
    I_numeric, _ = quad(f, 0, 1) # Integral using quadratures
    
    # Compare it to the value obtained using a Monte Carlo simulation
    
    
needle_dropping()
