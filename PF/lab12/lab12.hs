import Distribution.SPDX (LicenseId(DOC))
{- Monada Maybe este definita in GHC.Base 

instance Monad Maybe where
  return = Just
  Just va  >>= k   = k va
  Nothing >>= _   = Nothing


instance Applicative Maybe where
  pure = return
  mf <*> ma = do
    f <- mf
    va <- ma
    return (f va)       

instance Functor Maybe where              
  fmap f ma = pure f <*> ma   
-}

-- Ex1
pos :: Int -> Bool
pos  x = if (x>=0) then True else False

fct :: Maybe Int ->  Maybe Bool
fct  mx =  mx  >>= (\x -> Just (pos x))

-- Ex2
addM :: Maybe Int -> Maybe Int -> Maybe Int
addM mx my = mx >>= (\x -> Just (+x) <*> my)

-- Ex3
-- cartesian_product xs ys = xs >>= ( \x -> ys >>= (\y-> return (x,y)))
cartesianProduct xs ys = do 
    x <- xs
    y <- ys
    return (x, y)

-- prod f xs ys = [f x y | x <- xs, y<-ys]
prod f xs ys = do
  x <- xs
  y <- ys
  return (x, y)

-- myGetLine :: IO String
-- myGetLine = getChar >>= \x ->
--       if x == '\n' then
--           return []
--       else
--           myGetLine >>= \xs -> return (x:xs)

myGetLine :: IO String
myGetLine = do
  x <- getChar
  do
    if x == '\n' then
      return []
    else do
      y <- myGetLine
      return (x:y)

-- Ex 4
prelNo noin =  sqrt noin

-- ioNumber = do
--      noin  <- readLn :: IO Float
--      putStrLn $ "Intrare\n" ++ (show noin)
--      let  noout = prelNo noin
--      putStrLn $ "Iesire"
--      print noout

ioNumber = (readLn::IO Float) >>= (\noin -> putStrLn ("Intrare\n" ++ show noin) >> putStrLn "Iesire" 
                                   >> print (prelNo noin)  )


-- Ex 5
data MyWriter log a = MyWriter{runWriter :: (a, log)}

instance Show (MyWriter String Integer) where
  show (MyWriter (va, log)) = log ++ ": " ++ show va

instance Show (MyWriter String Int) where
  show (MyWriter (va, log)) = log ++ ": " ++ show va

instance Show (MyWriter String ()) where
  show (MyWriter (_, log)) = log

instance Show (MyWriter String String) where
  show (MyWriter (va, log)) = log ++ ": " ++ va

-- Print message
tell :: log -> MyWriter log ()
tell msg = MyWriter ((), msg)

instance Functor (MyWriter String) where
  fmap f (MyWriter (va, log)) = MyWriter (f va, log)

instance Applicative (MyWriter String) where
  pure va = MyWriter (va, "")
  (<*>) (MyWriter (fa, log1)) (MyWriter (va, log2)) = MyWriter (fa va, log1 ++ log2)

-- a), b)
instance Monad (MyWriter String) where
  return va = MyWriter (va, "")
  (>>=) mw1 f = 
    let 
      (va, log1) = runWriter mw1
      (vb, log2) = runWriter (f va)
    in MyWriter (vb, log1 ++ log2)

logIncrement :: Int -> MyWriter String Int
logIncrement va = tell ("Incrementing " ++ show va ++ " -->") >>= (\_ -> return (va + 1))

logIncrement2 :: Int -> MyWriter String Int
logIncrement2 va = logIncrement va >>= (\x -> logIncrement x)

logIncrementN :: Int -> Int -> MyWriter String Int
logIncrementN va 0 = return va
logIncrementN va n = logIncrement va >>= (\x -> logIncrementN x (n - 1))

x = MyWriter (5, "hello")
y = MyWriter (6, "World")

-- c)
data MyWriterLs log a = MyWriterLs{runWriterLs :: (a, [log])}
instance Functor (MyWriterLs String) where
  fmap f (MyWriterLs (va, log_ls)) = MyWriterLs (f va, log_ls)

instance Applicative (MyWriterLs String) where
  pure va = MyWriterLs (va, [])
  (<*>) (MyWriterLs (fa, log_list1)) (MyWriterLs (va, log_list2)) = MyWriterLs (fa va, log_list1 ++ log_list2)

instance Monad (MyWriterLs String) where
  return va = MyWriterLs (va, [])
  (>>=) mwl1 f = 
    let
      (va1, log_list1) = runWriterLs mwl1
      (va2, log_list2) = runWriterLs (f va1)
    in MyWriterLs (va2, log_list1 ++ log_list2)


-- Ex 6
data Person = Person { name :: String, age :: Int }

showPersonN :: Person -> String
showPersonN p = "Name: " ++ name p

showPersonA :: Person -> String
showPersonA p = "Age: " ++ show (age p)

showPerson :: Person -> String
showPerson p = show (tell ("(" ++ showPersonN p ++ ", ") >>= (\x -> tell (showPersonA p ++ ")")))

p = Person "ada" 20

-- Ex 7
newtype Reader env a = Reader { runReader :: env -> a }

instance Monad (Reader env) where
  return x = Reader (\_ -> x)
  ma >>= k = Reader f
    where f env = let a = runReader ma env
                  in  runReader (k a) env

instance Applicative (Reader env) where
  pure = return
  mf <*> ma = do
    f <- mf
    a <- ma
    return (f a)       

instance Functor (Reader env) where              
  fmap f ma = pure f <*> ma    

mshowPersonN ::  Reader Person String
mshowPersonN = Reader (\p -> "Name: " ++ name p) 
mshowPersonA ::  Reader Person String
mshowPersonA = Reader (\p -> "Age: " ++ show (age p))
mshowPerson ::  Reader Person String
mshowPerson = Reader (\p -> "(Name: " ++ name p ++ ", Age: " ++ show (age p) ++ ")")
{-
runReader mshowPersonN  $ Person "ada" 20
"NAME:ada"
runReader mshowPersonA  $ Person "ada" 20
"AGE:20"
runReader mshowPerson  $ Person "ada" 20
"(NAME:ada,AGE:20)"
-}