import Data.Char
len :: [a] -> Int
len [] = 0
len (h : t) = 1 + len t

verifL :: [a] -> Bool
verifL a = len a `mod` 2 == 0

takeFinal :: [a] -> Int -> [a]
takeFinal x 0 = []
takeFinal (h : t) n =
    if len (h : t) <= n
        then h : t
        else takeFinal t n
    
remove :: [a] -> Int -> [a]
remove x n = take (n - 1) x ++ drop n x

semiPareRec :: [Int] -> [Int]
semiPareRec [] = []
semiPareRec (h : t) = 
    if even h
        then (h `div` 2) : semiPareRec t
        else semiPareRec t

myReplicate :: Int -> a -> [a]
myReplicate 0 _ = []
myReplicate n x = x : myReplicate (n - 1) x

sumImp :: [Int] -> Int
sumImp [] = 0
sumImp (h : t) =
    if odd h
        then h + sumImp t
        else sumImp t

totalLen :: [String] -> Int
totalLen [] = 0
totalLen ((h1 : t1) : t2) =
    if h1 == 'A'
        then len (h1 : t1) + totalLen t2
        else totalLen t2


nrTotalVocale :: [String] -> Int
nrTotalVocale [] = 0
nrTotalVocale (h : t) =
    if isPalindrom h
        then nrVocale h + nrTotalVocale t
        else nrTotalVocale t

nrVocale :: String -> Int
nrVocale [] = 0
nrVocale (h : t) =
    if isVocala h
        then 1 + nrVocale t
        else nrVocale t

isPalindrom :: String -> Bool
isPalindrom a = a == reverse a

isVocala :: Char -> Bool
isVocala a = a `elem` "aeiouAEIOU"

adaugaDupaParN :: Int -> [Int] -> [Int]
adaugaDupaParN _ [] = []
adaugaDupaParN n (h : t) = 
    if even h
        then h : n : adaugaDupaParN n t
        else h : adaugaDupaParN n t

divizori :: Int -> [Int]
divizori n = [x | x <-[1..n], n `mod` x == 0]

listaDiv :: [Int] -> [[Int]]
listaDiv [] = []
listaDiv (h : t) = divizori h : listaDiv t

inIntervalRec :: Int -> Int -> [Int] -> [Int]
inIntervalRec _ _ [] = []
inIntervalRec l r (h : t) = 
    if h >= l && h <= r
        then h : inIntervalRec l r t
        else inIntervalRec l r t
        
inIntervalComp :: Int -> Int -> [Int] -> [Int]
inIntervalComp l r li = [x | x <- li, x >= l, x <= r]

pozitiveRec :: [Int] -> Int
pozitiveRec [] = 0
pozitiveRec (h : t) = 
    if h > 0
        then 1 + pozitiveRec t
        else pozitiveRec t

pozitiveComp :: [Int] -> Int
pozitiveComp l = len [x | x <- l, x > 0]

pozitiiImpareRec :: [Int] -> [Int]
pozitiiImpareRec [] = []
pozitiiImpareRec l = pozitiiImpareRecAux l 0

pozitiiImpareRecAux :: [Int] -> Int -> [Int]
pozitiiImpareRecAux [] _ = []
pozitiiImpareAux (h : t) n = 
    if odd h
        then n : pozitiiImpareRecAux t (n + 1)
        else pozitiiImpareRecAux t (n + 1)


pozitiiImpareComp :: [Int] -> [Int]
pozitiiImpareComp l = [snd x | x <- zip l [0..], odd (fst x)]

multDigitsRec :: String -> Int
multDigitsRec [] = 1
multDigitsRec (h : t) = 
    if isDigit h
        then digitToInt h * multDigitsRec t
        else multDigitsRec t

multDigitsComp :: String -> Int
multDigitsComp l = myProduct [digitToInt x | x <- l, isDigit x]


myProduct :: [Int] -> Int
myProduct [] = 1
myProduct (h : t) = h * myProduct t
