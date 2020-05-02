/**
 * Encapsulate methods and data related to the
 * Yunhetong API
 */
class Yht {
  loggedIn;
  url;
  appId;
  appKey;
  token;
  signerId;

  constructor (params) {
    if (Mh && Mh.debug) { console.debug('Creating YHT instance'); }
    this.url = params.url;
    this.appId = params.appId;
    this.appKey = params.appKey;
    this.loggedIn = false;
  }

  /**
   * Authenticate with the Yunhetong backend.
   * 2、令牌相关接口 p.14
   *
   * The API authenticates the current user for 15 minutes.
   * After the 11th minute it will send a must-revalidate header
   * in the response, it is the responsibility of the client to
   * monitor this header and refresh the login.
   *
   * Example request:
   * {
   *   "appId": "string",//应用 id,在开放平台注册应用获得
   *   "appKey": "string",//应用密码,在开放平台注册应用获得
   *   "signerId": "string"//用户 id,可选参数。如不传该参数,那么此时获取的是平台
   *   自身的长效令牌,反之获取的是指定用户的长效令牌
   * }
   *
   * Example response:
   * {
   *   "code": 200,//200 表示请求成功
   *   "msg": "成功"
   * }
   *
   * Any response code other than 200 indicates error, the msg will
   * have more information on the error type.
   *
   */
  async login () {
    const endpoint = this.url + 'auth/login';
    return await axios.post(endpoint, {
      appId: this.appId,
      appKey: this.appKey
    },{
      headers: {
        // Not valid but the server expects it
        'Content-Type':'application/json;charset=UTF-8',
      }
    }).then(res => {

      if (res.data.code === 200) {

        if (Mh && Mh.debug) { console.debug('Logged in against YHT backend', res); }
        this.loggedIn = true;

        // Extract the token
        const token = res.headers.token;
        if (!token || typeof token !== "string") {
          console.warn('Token is not valid', token);
        } else {
          this.token = token;
        }

        // Don't forget to return true to the component
        return true

      } else {
        console.warn(`Warning; got code ${res.data.code} in YHT response`);
        return false;
      }

    }).catch(err => {
      console.error(err);
      // Let the component know about the error
      return false;
    });
  }

  /**
   * Create a user that will be able to interact with the backend.
   * 3、用户相关接口 p.15
   *
   * Create a "personal user" and return the user's unique identifier
   * "signerId", this interface can only be used with the platform's
   * own long-term token is.
   *
   * Example request
   * {
   *   "userName": "string",//用户姓名(最长 15 字符)
   *   "identityRegion": "string",//身份地区:0 大陆,1 香港,2 台湾,3 澳门,4 外   *   籍人员
   *   "certifyType": "string", //证件类型:a 身份证, b 护照, d 港澳通行证, e 台胞证, f 港澳居民来往内地通行证, z 其他
   *   "certifyNum": "string",//身份证号码,应用内唯一。
   *   "phoneRegion": "string",//手机号地区:0 大陆,1 香港、澳门,2 台湾
   *   "phoneNo": "string",//手机号:1.大陆,首位为 1,长度 11 位纯数字;2.香港、澳门,长度为 8 的纯数字;3.台湾,长度为 10 的纯数字
   *   "caType": "string"//证书类型:B2 长效 CA 证书,固定字段
   * }
   *
   * Example 200 response
   * {
   *   "code": 200,
   *   "msg": true,
   *   "data": {
   *     "signerId": 349//用户 id,该 id 需要记录维护
   *   }
   * }
   *
   * Example error response
   * {
   *   "code": 20209
   *   "msg": "认证号码(330781198806025573)已经存在"
   * }
   *
   * @returns {Promise<string>}
   */
  async createUser (userData) {
    const endpoint = this.url + 'user/person';

    if (!this.token) {
      throw new Error('YHT token not set; cannot post create user request');
    }

    const options = {
      headers: {
        'Content-Type':'application/json;charset=UTF-8',
        'token': this.token
      }
    };

    return await axios.post(endpoint, userData, options).then(res => {

      // Check the status
      if (res.data.code === 200) {
        if (Mh && Mh.debug) { console.log('Create user success', res); }
        this.signerId = res.data.data.signerId;
        return userData.userName;
      } else {
        console.warn(`Warning; got code ${res.data.code} in YHT response`, res);
        return false;
      }
    }).catch(err => {
      console.warn(err);
      return false;
    });

  }
}
