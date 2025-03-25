data Fuel a = Fuel {getFuel :: Integer -> Integer -> Maybe (Integer, a)}

-- Commented because there was no time to write the funtor and applicative
-- instance Monad Fuel where
--     --return = pure
--     (>>=) :: Fuel a -> (a -> Fuel b) -> Fuel b
--     (>>=) m1 f = Fuel f where
--         f curr max = let
--             (Just (x, y)) = getFuel m1 curr max
--             in f x max

-- instance Functor Fuel where
--     ...

-- instance Applicative Fuel where
--     ...
