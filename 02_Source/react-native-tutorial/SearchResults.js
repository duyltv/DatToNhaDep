'use strict';

import React, { Component } from 'react'
import { SERVER } from './configs'
import { Message } from './message_communication'
import {
  StyleSheet,
  Image,
  View,
  TouchableHighlight,
  ListView,
  Text
} from 'react-native';

var styles = StyleSheet.create({
  thumb: {
    width: 80,
    height: 80,
    marginRight: 10
  },
  textContainer: {
    flex: 1
  },
  separator: {
    height: 1,
    backgroundColor: '#dddddd'
  },
  price: {
    fontSize: 25,
    fontWeight: 'bold',
    color: '#48BBEC'
  },
  title: {
    fontSize: 20,
    color: '#656565'
  },
  address: {
    fontSize: 13,
    color: '#656565'
  },
  rowContainer: {
    flexDirection: 'row',
    padding: 10
  }
});



class SearchResults extends Component {
  static navigationOptions = {
    title: 'Kết quả tìm kiếm',
  };

  constructor(props) {
    super(props);
    var dataSource = new ListView.DataSource(
      {rowHasChanged: (r1, r2) => r1.lister_url !== r2.lister_url});
    this.state = {
      dataSource: dataSource.cloneWithRows(this.props.navigation.state.params.listings)
    };
  }

  renderRow(rowData, sectionID, rowID) {
    var price = rowData.price;
    if (price > 1000)
    {
      price = price / 1000;
      price = price + " tỷ";
    }
    else
    {
      price = price + " triệu";
    }

    var avatar = SERVER + "/" + rowData.avatar;

    return (
      <TouchableHighlight onPress={() => this.rowPressed(rowData.content_id)}
          underlayColor='#dddddd'>
        <View>
          <View style={styles.rowContainer}>
            <Image style={styles.thumb} source={{ uri: avatar }} />
            <View  style={styles.textContainer}>
              <Text style={styles.title}
                    numberOfLines={1}>{rowData.title}</Text>
              <Text style={styles.price}>{price}</Text>
              <Text style={styles.address}>{rowData.address}</Text>
            </View>
          </View>
          <View style={styles.separator}/>
        </View>
      </TouchableHighlight>
    );
  }

  async getContent(content_id)
  {
    var input_data = {
      "content_id": content_id
    };
    var content = await Message("get_content", input_data);
    content = content.data[0];

    return content;
  }

  async rowPressed(content_id) {
    var content = await this.getContent(content_id);
    console.log(content);

    this.props.navigation.navigate('PropertyView', {content: content} );
  }


  render() {
    return (
      <ListView
        dataSource={this.state.dataSource}
        renderRow={this.renderRow.bind(this)}/>
    );
  }

}

module.exports = SearchResults;
