import Data.Sequence (Seq(Empty))
-- IMPLEMENTING SOME DATA TYPES TO BE FUNCTORS, APPLICATIVES AND MONADS
-- MyMaybe
data MyMaybe a = MyNothing | MyJust a

instance Show (MyMaybe Integer) where
    show MyNothing = "Nothing"
    show (MyJust x) = show x

instance Functor MyMaybe where
    fmap _ MyNothing = MyNothing
    fmap f (MyJust x) = MyJust (f x)

instance Applicative MyMaybe where
    pure x = MyJust x
    (<*>) (MyJust f) (MyJust x) = MyJust (f x)
    (<*>) _ _ = MyNothing

instance Monad MyMaybe where
    return x = MyJust x
    (>>=) MyNothing _ = MyNothing
    (>>=) (MyJust x) f = f x


-- MyList
data MyList a = EmptyList | MyL a (MyList a) deriving(Show)
myConcat :: MyList a -> MyList a -> MyList a
myConcat EmptyList l = l
myConcat (MyL h1 t1) l = MyL h1 (myConcat t1 l)

myBigConcat :: MyList (MyList a) -> MyList a
myBigConcat EmptyList = EmptyList
myBigConcat (MyL h t) = myConcat h (myBigConcat t) 

x = (MyL 4 (MyL 5 (MyL 6 EmptyList)))
y = (MyL 1 (MyL 2 (MyL 3 EmptyList)))
z = MyL x (MyL y EmptyList)

instance Functor MyList where
    fmap _ EmptyList = EmptyList
    fmap f (MyL h t) = MyL (f h) (fmap f t)

instance Applicative MyList where
    pure x = MyL x EmptyList
    -- each function from the first list is applied to the second
    (<*>) (MyL f t1) (MyL h t2) = MyL (f h) (t1 <*> t2) 
    (<*>) _ _ = EmptyList -- When either is empty we stop

instance Monad MyList where
    return x = MyL x EmptyList
    (>>=) EmptyList _ = EmptyList
    (>>=) (MyL h t) f = myConcat (f h) (t >>= f) 

test_f :: Integer -> MyList Integer
test_f v = MyL (v + 1) EmptyList 


-- map :: (a -> b) -> [a] -> [b]
-- class (Functor f) => Applicative f where
--     pure  :: a -> f a
--     (<*>) :: f (a -> b) -> f a -> f b

-- class Monad m where
--     (>>=)  :: m a -> (a -> m b) -> m b
--     return :: a -> m a