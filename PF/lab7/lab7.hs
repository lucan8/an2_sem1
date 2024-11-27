data Expr = Const Int -- integer constant
          | Expr :+: Expr -- addition
          | Expr :*: Expr -- multiplication
           deriving Eq

data Operation = Add | Mult deriving (Eq, Show)

data Tree = Lf Int -- leaf
          | Node Operation Tree Tree -- branch
           deriving (Eq, Show)
           
instance Show Expr where
  show (Const x) = show x
  show (e1 :+: e2) = "(" ++ show e1 ++ " + "++ show e2 ++ ")"
  show (e1 :*: e2) = "(" ++ show e1 ++ " * "++ show e2 ++ ")"           

evalExp :: Expr -> Int
evalExp(Const i) = i
evalExp(e1 :+: e2) = evalExp e1 + evalExp e2
evalExp(e1 :*: e2) = evalExp e1 * evalExp e2

exp1 = ((Const 2 :*: Const 3) :+: (Const 0 :*: Const 5))
exp2 = (Const 2 :*: (Const 3 :+: Const 4))
exp3 = (Const 4 :+: (Const 3 :*: Const 3))
exp4 = (((Const 1 :*: Const 2) :*: (Const 3 :+: Const 1)) :*: Const 2)
test11 = evalExp exp1 == 6
test12 = evalExp exp2 == 14
test13 = evalExp exp3 == 13
test14 = evalExp exp4 == 16

evalArb :: Tree -> Int
evalArb(Lf val) = val
evalArb(Node Mult (Lf v1) (Lf v2)) = evalArb(Lf v1) * evalArb(Lf v2)
evalArb(Node Add (Lf v1) (Lf v2)) = evalArb(Lf v1) + evalArb(Lf v2)
evalArb(Node Add t1 t2) = evalArb t1 + evalArb t2
evalArb(Node Mult t1 t2) = evalArb t1 * evalArb t2


arb1 = Node Add (Node Mult (Lf 2) (Lf 3)) (Node Mult (Lf 0)(Lf 5))
arb2 = Node Mult (Lf 2) (Node Add (Lf 3)(Lf 4))
arb3 = Node Add (Lf 4) (Node Mult (Lf 3)(Lf 3))
arb4 = Node Mult (Node Mult (Node Mult (Lf 1) (Lf 2)) (Node Add (Lf 3)(Lf 1))) (Lf 2)

test21 = evalArb arb1 == 6
test22 = evalArb arb2 == 14
test23 = evalArb arb3 == 13
test24 = evalArb arb4 == 16


expToArb :: Expr -> Tree
expToArb(Const i) = Lf i
expToArb(e1 :+: e2) = Node Add (expToArb e1) (expToArb e2)
expToArb(e1 :*: e2) = Node Mult (expToArb e1) (expToArb e2)


data IntSearchTree value
  = Empty
  | BNode
      (IntSearchTree value)     -- elemente cu cheia mai mica
      Int                       -- cheia elementului
      (Maybe value)             -- valoarea elementului
      (IntSearchTree value)     -- elemente cu cheia mai mare
  
lookup' :: Int -> IntSearchTree value -> Maybe value
lookup' s_key Empty = Nothing
lookup' s_key (BNode left curr_key ret_val right) = 
    if curr_key == s_key
        then ret_val
    else if curr_key < s_key
        then lookup' s_key left
    else lookup' s_key right


keys ::  IntSearchTree value -> [Int]
keys root = [fst pair | pair <- toList root]

values :: IntSearchTree value -> [value]
values root = [snd pair | pair <- toList root]

insert :: Int -> value -> IntSearchTree value -> IntSearchTree value
insert s_key val Empty = BNode Empty s_key (Just val) Empty
insert s_key val (BNode left curr_key ret_val right)
 |s_key < curr_key = BNode (insert s_key val left) curr_key ret_val right
 |s_key > curr_key = BNode left curr_key ret_val (insert s_key val left)
 |otherwise = BNode left curr_key ret_val right

delete :: Int -> IntSearchTree value -> IntSearchTree value
delete s_key (BNode left curr_key ret_val right)
 | s_key < curr_key = BNode (delete s_key left) curr_key ret_val right
 | s_key > curr_key = BNode left curr_key ret_val (delete s_key left)
 | otherwise = Empty

toList :: IntSearchTree value -> [(Int, value)]
toList Empty = []
toList (BNode left key (Just val) right) = (toList left) ++ [(key, val)] ++ toList(right)

fromList :: [(Int,value)] -> IntSearchTree value
fromList [] = Empty 
fromList (x : xs) = insert (fst x) (snd x) (fromList xs)

printTree :: IntSearchTree Int -> String
printTree Empty = ""
printTree (BNode left key (Just val) right) =  "(" ++ show key ++ " " ++ show val ++ ") " ++
                                               (printTree left) ++ " " ++ (printTree right)
printTree (BNode left key Nothing right) = (printTree left) ++ " " ++ (printTree right)

-- balance :: IntSearchTree value -> IntSearchTree value
-- balance = undefined