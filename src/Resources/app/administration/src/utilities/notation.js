const get = (t, path) =>
  path.split(".").reduce((r, k) => r?.[k], t)

const set = (t, path, value) => {
    if (typeof t != "object") return
    if (path == "") throw Error("empty path")

    const pos = path.indexOf(".")
    const paths = path.split('.')

    /// if path is undefined create empty object
    if (t[paths[0]] === undefined) {
        t[paths[0]] = {}
    }

    return pos == -1
        ? (t[path] = value, value)
        : set(t[path.slice(0, pos)], path.slice(pos + 1), value) 
}

export {
    get,
    set
}