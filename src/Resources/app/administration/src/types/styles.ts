export type TextAlign = 'left' | 'center' | 'right';

export type JustifyContent =
    | 'normal'
    | 'stretch'
    | 'flex-start'
    | 'center'
    | 'flex-end'
    | 'space-between'
    | 'space-around';

export type AlignItems = 'stretch' | 'flex-start' | 'center' | 'flex-end';

export type ObjectFit = 'cover' | 'contain' | 'auto';

export type ObjectPosition = 'center';

export type ObjectPositionX = ObjectPosition | 'left' | 'right';

export type ObjectPositionY = ObjectPosition | 'top' | 'bottom';

export type BgGradient = {
    startColor: string | null;
    endColor: string | null;
    gradientType: 'linear-gradient';
    gradientDeg: number;
};

export type BgEffect = {
    blur: string | null;
};
