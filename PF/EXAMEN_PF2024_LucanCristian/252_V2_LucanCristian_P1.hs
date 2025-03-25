import Data.Text.Array (equal)
-- equalList :: [Integer] -> [Integer] -> Bool
-- equalList (x:xs) (y:ys) = (x == y) && equalList xs ys

-- Check if legnth is odd and list contains only "valid" elements(x > m || x < n)
isValidList :: Integer -> Integer -> [Integer]  -> Bool
isValidList n m l = (length (filter (\x -> x > m || x < n) l) == length l) && ((length l) `mod` 2 == 1)

-- Keep only valid lists and concatenate them
coolConcat :: [[Integer]] -> Integer -> Integer -> [Integer]
coolConcat l n m = concat (filter (isValidList n m) l)

-- Test
x = [[1,2,3], [2, 4, 1, 3], [11, 8, 8], [2, 3, 5, 6]]
n = 4
m = 7
res = coolConcat x n m