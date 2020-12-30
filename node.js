'use strict';
//const data ='{"data":"{\"dccTrackingNumbers\":\"LXBTH000006415611\"}","sign":"39865451ebbb24d7d35f5b017c99226f"}';
const data ='0123456789abcdefghlmnopq';
const password = 'akxmKpoR';
const crypto = require('crypto');
function bodyEncrypt(content, secretKey, charset = 'utf-8') {
    const iv = Buffer.from([0x12, 0x34, 0x56, 0x78, 0x90, 0xab, 0xcd, 0xef]);
    //const iv = Buffer.from([0x62, 0x75, 0x66, 0x66, 0x65, 0x72, 0x65, 0x72]);
    //const iv = 'bufferer';
    const cipher = crypto.createCipheriv('des-cbc', secretKey.substr(0, 8), iv);
    cipher.setAutoPadding(true);
    let encrypted = cipher.update(content, charset, 'base64');
    encrypted += cipher.final('base64');
    return encrypted;
}
let mima = bodyEncrypt(data,password);
console.log(mima);

// const crypto = require('crypto');
//
// const data = 'adddd';
// const password = '123456789';
//
// // 创建加密算法
// const aseEncode = function(data, password) {
//
//     const iv = Buffer.from([0x12, 0x34, 0x56, 0x78, 0x90, 0xab, 0xcd, 0xef]);
//
//     // 如下方法使用指定的算法与密码来创建cipher对象
//     const cipher = crypto.createCipheriv('des-cbc', password.substr(0, 8),iv);
//     //cipher.setAutoPadding(true);
//     // 使用该对象的update方法来指定需要被加密的数据
//     let crypted = cipher.update(data, 'utf-8', 'base64');
//
//     crypted += cipher.final('base64');
//
//     return crypted;
// };
//
// console.log(aseEncode(data, password)); // 输出 ebdf98c254b9aa5265f6d4a5e73f861d


