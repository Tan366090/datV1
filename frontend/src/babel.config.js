export default {
  presets: [
    ["@babel/preset-env", {
      targets: {
        node: "current"
      }
    }],
    "@babel/preset-typescript"
  ],
  plugins: [
    "@babel/plugin-proposal-class-properties",
    "@babel/plugin-proposal-object-rest-spread",
    "@babel/plugin-transform-runtime"
  ]
}; 