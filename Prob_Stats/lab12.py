import numpy as np
import matplotlib.pyplot as plt
def signalFunc(t):
    return np.sin(2 * t) + 0.3 * np.cos(10 * t) + 0.05 * np.sin(100 * t)

def getNormalVars(nr_vars, sigma):
    u1 = np.random.random(nr_vars)
    u2 = np.random.random(nr_vars)
    
    radius = np.sqrt(-2 * np.log(u1)) 
    theta = np.cos(2 * np.pi * u2)
    return radius * theta * sigma

def expectedValue(normal_vars):
    return sum(signalFunc(normal_vars)) / len(normal_vars)

nr_signal_points = 400
signal_time = 4
nr_normal_vars = 100
sigma = 0.1

#Initial function
ts = np.linspace(0, 4, nr_signal_points)
plt.plot(ts, signalFunc(ts))

#Smoothed function
xs = getNormalVars(nr_normal_vars, sigma)
smothed_points = [expectedValue(t - xs) for t in ts]
plt.plot(ts, smothed_points)

plt.show()








