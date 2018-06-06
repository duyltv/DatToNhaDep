'use strict';

import React, { Component } from 'react'
import ImageSlider from 'react-native-image-slider'
import { Button } from 'react-native';
import { SERVER } from './configs'
import { Base64 } from './Base64'
import {
  StyleSheet,
  Image,
  View,
  ScrollView,
  TouchableHighlight,
  Text
} from 'react-native';
import { Message } from './message_communication'

var styles = StyleSheet.create({
  container: {
    marginTop: 20
  },
  expand_container: {
    marginTop: 20
  },
  heading: {
    backgroundColor: '#F8F8F8',
  },
  separator: {
    height: 1,
    backgroundColor: '#DDDDDD'
  },
  image: {
    width: 400,
    height: 300
  },
  price: {
    fontSize: 25,
    fontWeight: 'bold',
    margin: 5,
    color: '#48BBEC'
  },
  content: {
    fontSize: 15,
    margin: 5,
    color: '#656565'
  },
  expand_content: {
    fontSize: 15,
    margin: 5,
    color: '#656565'
  },
  title: {
    fontSize: 20,
    margin: 5,
    color: '#656565'
  },
  description: {
    fontSize: 18,
    margin: 5,
    color: '#656565'
  },
  buttonText: {
    fontSize: 18,
    color: 'white',
    alignSelf: 'center'
  },
  button: {
    height: 36,
    flex: 1,
    flexDirection: 'row',
    backgroundColor: '#48BBEC',
    borderColor: '#48BBEC',
    borderWidth: 1,
    borderRadius: 8,
    marginBottom: 10,
    alignSelf: 'stretch',
    justifyContent: 'center'
  },
  address: {
    fontSize: 13,
    color: '#656565'
  },
  buttonSelected: {
    opacity: 1,
    color: 'red',
  },
  customSlide: {
    backgroundColor: 'green',
    alignItems: 'center',
    justifyContent: 'center',
  },
  customImage: {
    width: 100,
    height: 100,
  }
});

class PropertyView extends Component {
  static navigationOptions = ({ navigation }) => ({
    title: `${navigation.state.params.content.title}`
  });

  async onLocationPressed() {

  }

  b64DecodeUnicode(str) {
    return decodeURIComponent(Array.prototype.map.call(Base64.atob(str), function(c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
    }).join(''));
  }

  async onMapPressed(address) {
    console.log('https://geocoder.cit.api.here.com/6.2/geocode.json?searchtext='+encodeURIComponent(address)+'&app_id=FBF6cDCEZaRWCPmaRukF&app_code=bMR-YearZ60Uiywcai_8jw&gen=1');
    let response = await fetch(
      'https://geocoder.cit.api.here.com/6.2/geocode.json?searchtext='+encodeURIComponent(address)+'&app_id=FBF6cDCEZaRWCPmaRukF&app_code=bMR-YearZ60Uiywcai_8jw&gen=1'
    ).catch((error) => {
      console.error(error);
    });

    if (response == undefined)
      return;

    console.log(response);
    let responseJson = await response.json();
    var long = await responseJson.Response.View[0].Result[0].Location.DisplayPosition.Longitude;
    var lat = await responseJson.Response.View[0].Result[0].Location.DisplayPosition.Latitude;
    var content = this.props.navigation.state.params.content;

    var price = content.price;
    if (price > 1000)
    {
      price = price / 1000;
      price = price + " tỷ";
    }
    else
    {
      price = price + " triệu";
    }
    await this.props.navigation.navigate('MapsPage',
    {
      longitude: long,
      latitude: lat,
      prop_name: content.title,
      prop_price: price
    });
  }

  render() {
    var content = this.props.navigation.state.params.content;
    var expand_content = content.expand_content;

    var price = content.price;
    if (price > 1000)
    {
      price = price / 1000;
      price = price + " tỷ";
    }
    else
    {
      price = price + " triệu";
    }

    var avatar = SERVER + "/" + content.avatar;
    var content_str = this.b64DecodeUnicode(content.content);

    var images_t = content.images;
    var images = [] ;
    images_t.map((image, index) => { images.push(SERVER+image.image_url) });
    console.log(images);

    console.log(content);

    return (
      <ScrollView style={styles.container}>
        <ImageSlider
          loopBothSides
          autoPlayWithInterval={3000}
          images={images}
          customSlide={({ index, item, style, width }) => (
            <View key={index} style={[style, styles.customSlide]}>
              <Image source={{ uri: item }} style={styles.customImage} />
            </View>
          )}
          customButtons={(position, move) => (
            <View style={styles.buttons}>
              {images.map((image, index) => {
                return (
                  <TouchableHighlight
                    key={index}
                    underlayColor="#ccc"
                    onPress={() => move(index)}
                    style={styles.button}
                  >
                    <Text style={position === index && styles.buttonSelected}>
                      {index + 1}
                    </Text>
                  </TouchableHighlight>
                );
              })}
            </View>
          )}
        />
        <View style={styles.heading}>
          <Text style={styles.title}>{content.title}</Text>
          <Text style={styles.price}>{price}</Text>
          <Button title="Mở bản đồ" onPress={() => this.onMapPressed(content.address)}/>
          <Text style={styles.address}>{content.address}</Text>
          <View style={styles.separator}/>
        </View>
        <View style={styles.container}>
          <Text style={styles.content}>{content_str}</Text>
          <View style={styles.separator}/>
        </View>
        <View style={styles.expand_container}>
          {expand_content.map((prop, key) => {
             return (
               <Text style={styles.expand_content} key={key}><Text style={{fontWeight: "bold"}} key={-key}>{prop.expand_name}</Text>: {prop.expand_content} {prop.measure_unit}</Text>
             );
          })}
          <View style={styles.separator}/>
        </View>
      </ScrollView>
    );
  }
}

module.exports = PropertyView;
