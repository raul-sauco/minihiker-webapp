const testUser = {
  userName: null,
  identityRegion: "0",
  certifyType: "a",
  certifyNum: "330781198806025573",
  phoneRegion: "0",
  phoneNo: "15777391910",
  caType: "B2"
};

// Experiment with the Yunhetong API
const yht = new Yht(Mh.globalData.yht);

new Vue({
  el: '#site-index',
  data: {
    yht,
    loggedIn: false,
    userName: null
  },
  mounted () {
  },
  methods: {
    /**
     * Login this service against the Yunhetong API
     */
    login: function () {
      yht.login().then(res => {
        this.loggedIn = res;
        console.log('It worked, we are logged in ' + res);
      }).catch(err => {
        console.log('Oh man! request failed.');
      });
    },
    createUser: function () {
      yht.createUser(testUser).then(res => {
        this.userName = res;
      }).catch(err => {
        console.error('Upss, some error creating the user, better check the logs');
      });
    }
  },
  computed: {
    loginStatus: function () {
      return this.loggedIn ? 'Logged In' : 'Not logged in';
    }
  },
  watch: {}
});
