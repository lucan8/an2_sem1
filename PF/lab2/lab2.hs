import Data.List
import Data.Array.IO.Safe (writeArray)
import GHC.Base (neChar)

double :: Integer -> Integer
double x = x + x

maxim2 :: Integer -> Integer -> Integer
maxim2 x y = if (x > y) then x else y


maxim3 :: Integer -> Integer -> Integer -> Integer
maxim3 x y z =
    if (x > y)
        then if (x > z)
            then x
            else z
        else if (y > z)
            then y
            else z

maxim4 :: Integer -> Integer -> Integer -> Integer -> Integer
maxim4 a b c d =
    let z = maxim2 a b
        t = maxim2 c d
    in maxim2 z t


verif :: Integer -> Integer -> Integer -> Integer -> Bool
verif a b c d =
    let z = maxim4 a b c d
    in z >= a && z >= b && z >= c && z >= d

binomial2 a b = a ^ 2 + b ^ 2
isPar :: Integer -> String
isPar a = if ((a `mod` 2) == 0)
            then "Par"
            else "Impar"

factorial :: Integer -> Integer
factorial 0 = 1
factorial 1 = 1
factorial a = a * factorial(a - 1)

--maxList :: [Ord] -> Ord
maxList [] = 0
maxList (h : t) =
    let y = maxList t
    in if (h > y)
        then h
        else y



poly :: Double -> Double -> Double -> Double -> Double
poly a b c x = a * x ^ 2 + b * x + c

tribonacii :: Integer -> Integer
tribonacii 1 = 1
tribonacii 2 = 1
tribonacii 3 = 2
tribonacii n = tribonacii (n - 3) + tribonacii (n - 1) + tribonacii (n - 2)


binomial :: Integer -> Integer -> Integer
binomial n 0 = 1
binomial 0 k = 0
binomial n k = binomial (n - 1) k + binomial (n - 1) (k - 1)

x = [1, 2, 3];
y = permutations(x);
z = subsequences(y);
a = 3
w = 7
t = double(a)
e = maxim2 a t
v = maxim2 a (maxim2 w e)