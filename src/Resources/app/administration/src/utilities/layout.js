const breakpoints = {
    md: 630,
    lg: 1024
}

const containerBreakpoints = {
    '3col': {
        [breakpoints.md]: {
            columns: '1fr',
            gap: '0 1rem'
        },
        [breakpoints.lg]: {
            columns: '1fr 1fr',
            gap: '0 1rem'
        }
    }
}

export {
    breakpoints,
    containerBreakpoints
}
