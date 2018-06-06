'use strict';

import React, { Component } from 'react'
import {
  StyleSheet,
  View
} from 'react-native';

import MapView from 'react-native-maps';

const styles = StyleSheet.create({
  container: {
    ...StyleSheet.absoluteFillObject,
    height: 400,
    width: 400,
    justifyContent: 'flex-end',
    alignItems: 'center',
  },
  map: {
    ...StyleSheet.absoluteFillObject,
  },
});

class MapsPage extends React.Component {
  render() {
    const { region } = this.props;
    const { params } = this.props.navigation.state;
    console.log(region);

    return (
      <View style ={styles.container}>
        <MapView
          style={styles.map}
          region={{
            latitude: params.latitude,
            longitude: params.longitude,
            latitudeDelta: 0.015,
            longitudeDelta: 0.0121,
          }}
        >
        </MapView>
      </View>
    );
  }
}

module.exports = MapsPage;
