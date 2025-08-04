const path = require('path');

module.exports = {
    mode: 'development', // або 'production' для оптимізації
    entry: './assets/app.js', // точка входу
    output: {
        filename: 'bundle.js', // ім'я зібраного файлу
        path: path.resolve(__dirname, 'public/build'), // куди збирати файли
        clean: true, // очищати папку перед збіркою
    },
    module: {
        rules: [
            {
                test: /\.css$/i,
                use: ['style-loader', 'css-loader'], // щоб імпортувати CSS з JS
            },
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader', // для підтримки сучасного JS, якщо потрібно
                    options: {
                        presets: ['@babel/preset-env'],
                    },
                },
            },
            {
                test: /\.(png|jpg|jpeg|gif|svg)$/i,
                type: 'asset/resource', // щоб працювати з картинками (якщо потрібно)
            },
        ],
    },
    devtool: 'source-map', // для дебагу
    devServer: {
        static: './public',
    },
};