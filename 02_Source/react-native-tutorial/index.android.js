'use strict';

import { Message } from './message_communication'

var React = require('react');
var ReactNative = require('react-native');
var ReactNavigation = require('react-navigation');

var SearchPage = require('./SearchPage');

var SearchResults = require('./SearchResults');

var PropertyView = require('./PropertyView');

var MapsPage = require('./MapsPage');

var styles = ReactNative.StyleSheet.create({
  text: {
    color: 'black',
    //backgroundColor: 'white',
    fontSize: 30,
    margin: 80,
    textAlign: 'center'
  },
  container_row: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    width: '90%'
  },
  button: {
    flex: 1,
    width: '40%',
    justifyContent: 'space-around',
    height: 50,
    left: '20%'
  }
});

class HomeScreen extends React.Component {
  static navigationOptions = {
    title: 'Welcome',
  };

  async getExpandList(type_id)
  {
    var input_data = {
      "type_id": type_id
    };
    var content = await Message("get_expand_content_define", input_data);
    content = await content.data;

    return content;
  }

  async rowPressed(type_id) {
    var type_list = await this.getExpandList(type_id);
    console.log(type_list);

    var type_name = "";

    if (type_id == 1)
      type_name = 'Nhà bán';
    else if (type_id == 2)
      type_name = 'Nhà cho thuê';
    else if (type_id == 3)
      type_name = 'Đất bán';
    else if (type_id == 4)
      type_name = 'Đất cho thuê';


    await this.props.navigation.navigate('SearchPage', {type_list: type_list, type_id: type_id, type_name: type_name} );
  }

  render() {
    const { navigate } = this.props.navigation;

    return (
      <ReactNative.View>
        <ReactNative.Text style={styles.text}>Đất To Nhà Đẹp . Com</ReactNative.Text>
        <ReactNative.View>
          <ReactNative.View style={styles.container_row}>
            <ReactNative.View style={styles.button}>
              <ReactNative.Button onPress={() => this.rowPressed(1)} title="Nhà bán" color="#841584" />
            </ReactNative.View>
            <ReactNative.View style={styles.button}>
              <ReactNative.Button onPress={() => this.rowPressed(2)} title="Nhà cho thuê" color="#841584" />
            </ReactNative.View>
          </ReactNative.View>
          <ReactNative.View style={styles.container_row}>
            <ReactNative.View style={styles.button}>
              <ReactNative.Button onPress={() => this.rowPressed(3)} title="Đất bán" color="#27a53c" />
            </ReactNative.View>
            <ReactNative.View style={styles.button}>
              <ReactNative.Button onPress={() => this.rowPressed(4)} title="Đất cho thuê" color="#27a53c" />
            </ReactNative.View>
          </ReactNative.View>
        </ReactNative.View>
      </ReactNative.View>
    );
  }
}

const PropertyFinderApp = ReactNavigation.StackNavigator({
  Home: { screen: HomeScreen },
  SearchPage: { screen: SearchPage },
  SearchResults: { screen: SearchResults },
  PropertyView: { screen: PropertyView },
  MapsPage: { screen: MapsPage }
});


ReactNative.AppRegistry.registerComponent('PropertyFinder', () => PropertyFinderApp );
