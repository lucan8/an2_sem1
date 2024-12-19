import Text.Read (Lexeme(Ident))
{-
class Functor f where
fmap : : ( a -> b ) -> f a -> f b
-}
newtype Identity a = Identity a

instance Functor Identity where
    fmap f (Identity v) = Identity (f v)

data Pair a = Pair a a

instance Functor Pair where
    fmap f (Pair left right) = Pair (f left) (f right) 

data Constant a b = Constant b

instance Functor (Constant a) where
    fmap f (Constant v) = Constant (f v)

data Two a b = Two a b

instance Functor (Two a) where
    fmap f (Two v1 v2) = Two v1 (f v2)

data Three a b c = Three a b c

instance Functor (Three a b) where
    fmap f (Three v1 v2 v3) = Three v1 v2 (f v3)

data Three' a b = Three' a b b

instance Functor (Three' a) where
    fmap f (Three' v1 v2 v3) = Three' v1 (f v2) (f v3)


data Four a b c d = Four a b c d

instance Functor (Four a b c) where
    fmap f (Four v1 v2 v3 v4) = Four v1 v2 v3 (f v4)

data Four'' a b = Four'' a a a b

instance Functor (Four'' a) where
    fmap f (Four'' v1 v2 v3 v4) = Four'' v1 v2 v3 (f v4)

data Quant a b = Finance | Desk a | Bloor b

instance Functor (Quant a) where
    fmap f Finance = Finance
    fmap f (Desk v) = Desk v
    fmap f (Bloor v) = Bloor (f v)

data LiftItOut f a = LiftItOut (f a)

instance Functor f => Functor (LiftItOut f) where
    fmap f (LiftItOut v1) = LiftItOut (fmap f v1)

data Parappa f g a = DaWrappa (f a) (g a)

instance (Functor f, Functor g) => Functor (Parappa f g) where
    fmap f (DaWrappa v1 v2) = DaWrappa (fmap f v1) (fmap f v2)

data IgnoreOne f g a b = IgnoringSomething (f a) (g b)

instance (Functor f, Functor g) => Functor (IgnoreOne f g a) where
    fmap f (IgnoringSomething v1 v2) = IgnoringSomething v1 (fmap f v2)

data Notorious g o a t = Notorious (g o) (g a) (g t)

instance Functor g => Functor (Notorious g o a) where
    fmap f (Notorious v1 v2 v3) = Notorious v1 v2 (fmap f v3)

w = Notorious (Just 5) (Just 1) (Just 2)

data GoatLord a = NoGoat | OneGoat a | MoreGoats (GoatLord a) (GoatLord a) (GoatLord a)

instance Show a => Show (GoatLord a) where
    show NoGoat = "NoGoat"
    show (OneGoat v) = "OneGoat " ++ show v 
    show (MoreGoats v1 v2 v3) = "MoreGoats " ++ show v1 ++ " " ++ show v2 ++ " " ++ show v3
    
instance Functor GoatLord where
    fmap f NoGoat = NoGoat
    fmap f (OneGoat v1) = OneGoat (f v1)
    fmap f (MoreGoats v1 v2 v3) = MoreGoats (fmap f v1) (fmap f v2) (fmap f v3)

x = NoGoat
y = OneGoat "Joe"
z = OneGoat "Alex"
t = MoreGoats x y z

data TalkToMe a = Halt | Print String a | Read (String -> a)

instance Show a => Show (TalkToMe a) where
    show Halt = "halt"
    show (Print s v) = "Printing " ++ s ++ " " ++show v
    show (Read f) = "Reading"


instance Functor TalkToMe where
    fmap f Halt = Halt
    fmap f (Print s v) = Print s (f v)
    fmap f (Read f1) = Read (f . f1) 