module.exports = function(api){
  api.cache(false)
  return {
    
      "presets": [
        "@babel/preset-react",
        [
            "@babel/preset-env",
            {
                "targets": {
                    "ie": 11
                  }
                }
              ]
            ]
  }
}