'use strict';

import React, { Component } from 'react'
import { SERVER } from './configs'
import { Message } from './message_communication'
import {
  StyleSheet,
  Text,
  TextInput,
  View,
  TouchableHighlight,
  ActivityIndicator,
  Image,
  ScrollView
} from 'react-native';

var styles = StyleSheet.create({
  description: {
    marginBottom: 20,
    fontSize: 18,
    textAlign: 'center',
    color: '#656565'
  },
  container: {
    padding: 30,
    marginTop: 35
  },
  flowRight: {
    flexDirection: 'row',
    alignItems: 'center',
    alignSelf: 'stretch'
  },
  buttonText: {
    fontSize: 18,
    color: 'white',
    alignSelf: 'center'
  },
  button: {
    height: 36,
    flexDirection: 'row',
    backgroundColor: '#48BBEC',
    borderColor: '#48BBEC',
    borderWidth: 1,
    borderRadius: 8,
    marginBottom: 10,
    alignSelf: 'stretch',
    justifyContent: 'center'
  },
  searchInput: {
    height: 36,
    padding: 4,
    marginRight: 5,
    fontSize: 18,
    borderWidth: 1,
    borderColor: '#48BBEC',
    borderRadius: 8,
    color: '#48BBEC'
  },
  image: {
    width: 217,
    height: 138
  },
  scrol_container: {
    marginTop: 20
  },
  separator: {
    height: 1,
    backgroundColor: '#DDDDDD'
  }
});

class SearchPage extends Component {

  static navigationOptions = ({ navigation }) => ({
    title: `${navigation.state.params.type_name}`
  });

  constructor(props) {
    super(props);
    this.state = {
      searchString: 'london',
      isLoading: false,
      message: ''
    };
  }

  onSearchTextChanged(event) {
    console.log('onSearchTextChanged');
    this.setState({ searchString: event.nativeEvent.text });
    console.log(this.state.searchString);
  }

  async _executeQuery() {
    this.setState({ isLoading: true });
    var input_data = {
      type_id: this.props.navigation.state.params.type_id
    };
    var result = await Message("get_content_list", input_data);
    this._handleResponse(result);

  }

  onSearchPressed() {
    this._executeQuery();
  }

  _handleResponse(response) {
    this.setState({ isLoading: false , message: '' });
    console.log(response);
    if (response.status == 'success') {
      this.props.navigation.navigate('SearchResults', {listings: response.data});
    } else {
      this.setState({ message: 'Location not recognized; please try again.'});
    }
  }

  onLocationPressed() {
    navigator.geolocation.getCurrentPosition(
      location => {
        var search = location.coords.latitude + ',' + location.coords.longitude;
        this.setState({ searchString: search });
        this._executeQuery();
      },
      error => {
        this.setState({
          message: 'There was a problem with obtaining your location: ' + error
        });
      });
  }

  render() {
    var spinner = this.state.isLoading ?
      ( <ActivityIndicator
          size='large'/> ) :
      ( <View/>);

    var type_list = this.props.navigation.state.params.type_list;

    console.log('SearchPage.render');
    return (
      <ScrollView style={styles.scrol_container}>
        <Text style={styles.description}>
          Tìm kiếm {this.props.navigation.state.params.type_name} ngay:
        </Text>
        <View style={styles.container}>
          {type_list.map((prop, key) => {
             return (
               <TextInput style={styles.searchInput} key={key} placeholder={prop.expand_name+" ("+prop.measure_unit+")"} />
             );
          })}
          <TouchableHighlight
              style={styles.button}
              onPress={this.onSearchPressed.bind(this)}
              underlayColor='#99d9f4'>
            <Text style={styles.buttonText}>Tìm kiếm</Text>
          </TouchableHighlight>
        </View>
        {spinner}
        <Text style={styles.description}>{this.state.message}</Text>
      </ScrollView>
    );
  }
}

module.exports = SearchPage;
