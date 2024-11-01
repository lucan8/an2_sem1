x = [ x ^ 2 |x <- [1..10], x `rem` 3 == 2 ]
y = [ (x,y) | x <- [1..5], y <- [x..(x+2)] ]
z = [ (x,y) | x <- [1..3], let k = x ^ 2, y <- [1..k] ]
t = [ x | x <- "Facultatea de Matematica si Informatica", elem x ['A'..'Z'] ]
r = [ [x..y] | x <- [1..5], y <- [1..5], x < y ]

factori :: Int -> [Int]
factori n = [x | x <- [1..n], n `mod` x == 0]

prim :: Int -> Bool
prim n = length (factori n) == 2

numerePrime :: Int -> [Int]
numerePrime n = [x | x <- [2..n], prim x]

firstEl :: [(a, b)] -> [a]
firstEl l = map fst l

sumList :: [[Int]] -> [Int]
sumList l = map sum l

prel2 :: [Int] -> [Int]
prel2 l = map magic l

magic :: Int -> Int
magic n = if even n 
            then n `div` 2
            else n * 2

filterChar :: Char -> [[Char]] -> [String]
filterChar c l = filter (elem c) l

transformOdd :: [Int] -> [Int]
transformOdd l = map (^2) (filter odd l)


oddIndexSquared :: [Int] -> [Int]
oddIndexSquared l = map ((^2) . fst) (filter magic2 (zip l [1..length l]))

magic2 :: (Int, Int) -> Bool
magic2 (x, y) = odd y

onlyVowels :: [String] -> [String]
onlyVowels l = map (filter isVowel) l

isVowel :: Char -> Bool
isVowel c = elem c "aeiouAEIOU"

myMap :: (a -> b) -> [a] -> [b]
myMap f [] = []
myMap f (h:t) = (f h) : myMap f t

myFilter :: (a -> Bool) -> [a] -> [a]
myFilter f [] = []
myFilter f (h:t) = 
    if f h
        then h : myFilter f t
        else myFilter f t

main :: IO ()
main = do
    print (factori 10)
