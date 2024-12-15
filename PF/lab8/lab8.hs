import Data.Functor.Product (Product(Pair))
class Collection c where
  empty :: c key value
  singleton :: key -> value -> c key value
  insert :: Ord key
      => key -> value -> c key value -> c key value
  clookup :: Ord key => key -> c key value -> Maybe value
  delete :: Ord key => key -> c key value -> c key value

  keys :: c key value -> [key]
  keys col = [fst v| v <- (toList col)]

  values :: c key value -> [value]
  values col = [snd v| v <- (toList col)]

  toList :: c key value -> [(key, value)]

  fromList :: Ord key => [(key,value)] -> c key value
  fromList (x : xs) = insert  (fst x) (snd x) (fromList xs) 

data PairList k v
  = PairList {getPairList :: [(k, v)]}

instance Collection PairList where
  empty = PairList []
  singleton k v = PairList [(k, v)]
  insert k v col = PairList ((toList col) ++ [(k, v)])
  toList (PairList l) = l

  clookup sk (PairList []) = Nothing
  clookup sk (PairList (p : xs))
    | sk == fst p = Just (snd p)
    | otherwise = clookup sk (PairList xs)
  
  delete rk (PairList l) = PairList (filter ((/=rk) . fst) l)


x = PairList [(1,2), (2,3), (4,5)]


data SearchTree key value
  = Empty
  | BNode
      (SearchTree key value) -- elemente cu cheia mai mica
      key                    -- cheia elementului
      (Maybe value)          -- valoarea elementului
      (SearchTree key value) -- elemente cu cheia mai mare


data Punct = Pt [Int]

pToList (Pt l) = l
-- Helper function for show
inner_show :: Punct -> String
inner_show (Pt [x]) = show x
inner_show (Pt (x:xs)) =  (show x) ++ ", " ++  inner_show (Pt xs)

instance Show Punct where
  show p = "(" ++ inner_show p ++  ")"  

data Arb = Vid | F Int | N Arb Arb
          deriving Show

class ToFromArb a where
  toArb :: a -> Arb
  fromArb :: Arb -> a

temp_pt = Pt [1,2,3]
temp_arb = toArb temp_pt

instance ToFromArb Punct where
  toArb :: Punct -> Arb
  toArb (Pt []) = Vid
  toArb (Pt (x:xs)) = N (F x) (toArb (Pt xs))
  fromArb :: Arb -> Punct
  fromArb Vid = Pt []
  fromArb (F v) = Pt [v]
  fromArb (N a1 a2) = Pt (pToList (fromArb a1) ++ pToList (fromArb a2))

temp_pt1 = fromArb temp_arb :: Punct

-- Pt [1,2,3]
-- (1, 2, 3)

-- Pt []
-- ()

-- toArb (Pt [1,2,3])
-- N (F 1) (N (F 2) (N (F 3) Vid))
-- fromArb $ N (F 1) (N (F 2) (N (F 3) Vid)) :: Punct
--  (1,2,3)
data Geo a = Square a | Rectangle a a | Circle a
    deriving Show

class GeoOps g where
  perimeter :: (Floating a) => g a -> a
  area :: (Floating a) =>  g a -> a

instance GeoOps Geo where
  perimeter (Square l) = 4 * l
  perimeter (Rectangle l1 l2) = 2 * (l1 + l2)
  perimeter (Circle r) = 2 * pi * r

  area (Square l) = l * l
  area (Rectangle l1 l2) = l1 * l2
  area (Circle r) = pi * r * r

-- ????
instance Eq (Geo l) where
  (==) g1 g2 = (perimeter g1) == (perimeter g2) 

-- ghci> pi
-- 3.141592653589793
