--1
sumPI :: [Int] -> Int
sumPI l = foldr (+) 0 (map (^2) (filter odd l))

--2
allTrue :: [Bool] -> Bool
allTrue l = length l == foldr (+) 0 (map fromEnum l)

--3
allVerifies :: (Int -> Bool) -> [Int] -> Bool
allVerifies f l = allTrue (map f l)

--4
anyTrue :: [Bool] -> Bool
anyTrue l = foldr (+) 0 (map fromEnum l) /= 0

anyVerifies :: (Int -> Bool) -> [Int] -> Bool
anyVerifies f l = anyTrue (map f l)

--5
mapFoldr :: (a -> b) -> [a] -> [b]
mapFoldr f l = foldr ((:) . f) [] l 

myConcat :: (a -> Bool) -> a -> [a] -> [a]
myConcat f x l =
    if f x
        then x : l
        else l


foldrFilter :: (a -> Bool) -> [a] -> [a]
foldrFilter f l = foldr (myConcat f) [] l

--6
listToInt :: [Int] -> Int
listToInt l = foldl ((+) . (*10)) 5 l


--7
rmChar :: Char -> String -> String
rmChar c s = filter (/= c) s

rmCharRec :: String -> String -> String
rmCharRec [] s2 = s2
rmCharRec (x : xs) s2 = rmCharRec xs (rmChar x s2)

rmCharFoldr :: String -> String -> String
rmCharFoldr removable_chars input = foldr rmChar input removable_chars 

--8
appendFront :: [Int] -> [Int] -> [Int]
appendFront l1 l2 = l2 ++ l1

myReverse :: [Int] -> [Int]
myReverse l = foldr appendFront [4] [[x] | x <- l]

--9
myElem :: [Int] -> Int -> Bool
myElem l val = length (filter (== val) l) /= 0

--10
myUnzipRec :: [(a, b)] -> ([a], [b])
myUnzipRec [] = ([], [])
myUnzipRec (x : xs) = 
    let
        next = myUnzipRec xs
    in
        (fst x : fst next, snd x : snd next)

doubleAppend :: (a, b) -> ([a], [b]) -> ([a], [b])
doubleAppend pair l_pair = (fst pair : fst l_pair, snd pair : snd l_pair)


myUnzip :: [(a, b)] -> ([a], [b])
myUnzip l = foldr doubleAppend ([], []) l

--11

merge :: [Int] -> [Int] -> [Int]
merge l1 [] = l1
merge [] l2 = l2
merge (x1 : xs1) (x2 : xs2)
    | x1 < x2 = x1 : merge xs1 (x2 : xs2)
    | x1 > x2 = x2 : merge xs2 (x1 : xs1)
    | otherwise = x1 : merge xs1 xs2



halfList :: [Int] -> Int -> ([Int], [Int])
halfList [] n = ([], [])
halfList (x : xs) n = 
    let next = halfList xs n
    in if length (x : xs) <= n `div` 2
        then (fst next, x : snd next)
        else (x : fst next, snd next)

mergeSort :: [Int] -> [Int]
mergeSort [x] = [x]
mergeSort l = 
    let 
        halved = halfList l (length l)
        f_half = mergeSort (fst halved)
        s_half = mergeSort (snd halved)
    in merge f_half s_half

union :: [Int] -> [Int] -> [Int]
union l1 l2 = merge (mergeSort l1) (mergeSort l2) 

--12
intersectSorted :: [Int] -> [Int] -> [Int]
intersectSorted l1 [] = []
intersectSorted [] l2 = []
intersectSorted (x1 : xs1) (x2 : xs2)
    | x1 < x2 = intersectSorted xs1 (x2 : xs2)
    | x1 > x2 = intersectSorted xs2 (x1 : xs1)
    | otherwise = x1 : intersectSorted xs1 xs2


intersect :: [Int] -> [Int] -> [Int]
intersect l1 l2 = intersectSorted (mergeSort l1) (mergeSort l2)