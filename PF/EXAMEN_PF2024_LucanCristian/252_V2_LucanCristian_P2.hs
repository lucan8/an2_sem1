data RGB v = RGB {getRed :: v, getGreen :: v, getBlue :: v} deriving Show

-- Helper to blend a single pixel
blendRGB :: Num v => (v -> v -> v) -> RGB v -> RGB v -> RGB v
blendRGB f (RGB r1 g1 b1) (RGB r2 g2 b2) = RGB (f r1 r2) (f g1 g2) (f b1 b2)

newtype Image p = Img [[p]] deriving Show

-- Helper to blend a row
blendRow :: Num v => (v -> v -> v) -> [RGB v] -> [RGB v] -> [RGB v]
blendRow f l1 l2 = map (\(x, y) -> blendRGB f x y) (zip l1 l2)

class Composite c where
    blend :: Num v => (v -> v -> v) -> c (RGB v) -> c (RGB v) -> c (RGB v)

instance Composite Image where
    blend f (Img l1) (Img l2) = Img (map (\(l1, l2) -> blendRow f l1 l2) (zip l1 l2))

-- For test
avg :: Int -> Int -> Int
avg x y = (x + y) `div` 2

avg1 :: Integer -> Integer -> Integer
avg1 x y = (x + y) `div` 2

-- Test
img1 = Img [[RGB 255 0 0, RGB 0 255 0, RGB 0 0 255]]
img2 = Img [[RGB 0 255 0, RGB 255 0 0, RGB 0 0 255]]
result = blend avg1 img1 img2
