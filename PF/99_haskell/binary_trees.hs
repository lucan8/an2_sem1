data Tree a = Empty | Branch a (Tree a) (Tree a)
              deriving (Show, Eq)

instance Functor Tree where
    fmap f Empty = Empty
    fmap f (Branch v left right) = Branch (f v) (fmap f left) (fmap f right)

instance Applicative Tree where
    pure v = Branch v Empty Empty
    (<*>) Empty t = Empty
    (<*>) t Empty = Empty
    (<*>) (Branch f _ _) t = fmap f t

instance Monad Tree where 
    return = pure
    (>>=) (Branch v left right) f = f v


-- P56
mirror :: Tree a -> Tree a -> Bool
mirror Empty Empty = True
mirror Empty _ = False
mirror _ Empty = False
mirror (Branch _ left1 right1) (Branch _ left2 right2) = mirror left1 right2 && mirror left2 right1

symmetric :: Tree a -> Bool
symmetric Empty = True
symmetric (Branch v left right) = mirror left right

-- P57
insert :: Int -> Tree Int -> Tree Int
insert new_v Empty = Branch new_v Empty Empty
insert new_v (Branch curr_v left right) 
    | new_v == curr_v = Branch curr_v left right
    | new_v < curr_v = Branch curr_v (insert new_v left) right
    | new_v > curr_v = Branch curr_v left (insert new_v right)

x = insert 7 Empty
fromList :: [Int] -> Tree Int
fromList [] = Empty
fromList (x:xs) = insert x (fromList xs)

collectLeaves :: Tree Integer -> [Integer]
collectLeaves Empty = []
collectLeaves (Branch v Empty Empty) = [v]
collectLeaves (Branch _ left right) = collectLeaves left ++ collectLeaves right

countLeaves :: Tree Integer -> Int
countLeaves t = length (collectLeaves t)

collectAll :: Tree Integer -> [Integer]
collectAll Empty = []
collectAll (Branch v left right) = collectAll left ++ [v] ++ collectAll right

distanceList :: Tree Integer -> Integer -> [(Integer, Integer)]
distanceList Empty _ = []
distanceList (Branch v left right) curr_d = distanceList left (curr_d + 1)
                                            ++ [(v, curr_d)] ++ 
                                            distanceList right (curr_d + 1)

atLevel :: Tree Integer -> Integer -> [Integer]
atLevel t level = map fst (filter (\x-> snd x == level) (distanceList t 1))
tree4 = Branch 1 (Branch 2 Empty (Branch 4 Empty Empty))
                 (Branch 2 Empty Empty)
