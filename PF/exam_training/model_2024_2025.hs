data Point = Pt [Int] deriving Show

-- Ex1
addPoint :: Int -> Point -> Point
addPoint v (Pt l) = Pt (l ++ [v])

mergePoints :: Point -> Point -> Point
mergePoints (Pt l1) (Pt l2) = Pt (l1 ++ l2)

data Arb = Empty | Node Int Arb Arb deriving Show
insert :: Int -> Arb -> Arb
insert v Empty = Node v Empty Empty
insert v (Node curr_v left right)
    | v == curr_v = Node curr_v left right -- Nothing happens if value exists already
    | v < curr_v = Node curr_v (insert v left) right
    | v > curr_v = Node curr_v left (insert v right)

class ToFromArb a where
    toArb :: a -> Arb
    fromArb :: Arb -> a

instance ToFromArb Point where
    toArb (Pt (x:xs)) = insert x (toArb (Pt xs))
    fromArb Empty = Pt []
    fromArb (Node v left right) =  mergePoints (addPoint v (fromArb left)) (fromArb right)


-- Ex2
makeInterval :: Int -> Int -> [Int] -> [Int]
makeInterval _ _ [] = []
makeInterval start end (x:xs)
    | x >= start && x <= end = x : makeInterval start end xs
    | otherwise = makeInterval start end xs


makeInterval1 :: Int -> Int -> [Int] -> [Int]
makeInterval1 start end l = l >>= (\x -> if x >= start && x <= end then [x] else [])

-- Ex3
newtype ReaderWriter env a = RW {getRW :: env-> (a,String)}

instance Functor (ReaderWriter env) where
    fmap f rw = RW $ \env ->
        let 
            (v, s) = getRW rw env
        in (f v, s)

instance Applicative (ReaderWriter env) where
    pure v = RW $ \env -> (v, "")
    (<*>) rwf rw = RW $ \env ->
        let 
            (f, s1) = getRW rwf env
            (v, s2) = getRW rw env
        in (f v, s1 ++ s2)

instance Monad (ReaderWriter env) where
    return = pure
    (>>=) rw f = RW $ \env ->
        let 
            (v, s1) = getRW rw env
            new_rw = f v
            (new_v, s2) = getRW new_rw env
        in (new_v, s1 ++ s2)

-- class Monad m where
--     (>>=)  :: m a -> (a -> m b) -> m b
--     return :: a -> m a
