export default function createNoCacheMiddleware() {
  const middleware = (options, next) => {
    const { headers = {}, path = '' } = options;

    if (!path.match(/nocache/)) {
      return next(options);
    }

    const {
      pragma = null,
      'cache-control': cacheControl = null,
    } = headers;

    if (pragma && cacheControl) {
      return next(options);
    }

    return next({
      ...options,
      headers: {
        ...headers,
        pragma: 'no-cache',
        'cache-control': 'no-cache',
      },
    });
  };

  return middleware;
}
