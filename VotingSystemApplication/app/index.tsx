import { useRef, useEffect } from "react";
import { Text, View, Animated, Dimensions, Image, StyleSheet } from "react-native";
import { router } from 'expo-router';

export default function Index() {
  const fadeAnim = useRef(new Animated.Value(0)).current;
  const loadingAnim = useRef(new Animated.Value(0)).current;
  const screenWidth = Dimensions.get('window').width;
  const lineWidth = screenWidth * 0.7;

  useEffect(() => {
    Animated.sequence([
      Animated.timing(fadeAnim, {
        toValue: 1,
        duration: 500,
        useNativeDriver: true,
      }),
      Animated.timing(loadingAnim, {
        toValue: 1,
        duration: 1000,
        useNativeDriver: false,
      }),
      Animated.delay(500),
      Animated.timing(fadeAnim, {
        toValue: 0,
        duration: 500,
        useNativeDriver: true,
      })
    ]).start(() => {
      router.replace('/login');
    });
  }, []);

  return (
    <View style={styles.container}>
      <Animated.View style={[{ opacity: fadeAnim }, styles.centered]}>
        <View style={styles.logoRow}>
          <Image
            source={require('../assets/images/logos/scc-logo.png')}
            style={styles.logo}
            resizeMode="contain"
          />
          <Image
            source={require('../assets/images/logos/cspc-logo.png')}
            style={styles.logo}
            resizeMode="contain"
          />
        </View>
        <Text style={styles.title}>CSPC E-Voting System</Text>
        <Text style={styles.subtitle}>Your Voice Matters</Text>
        <View style={styles.progressBarContainer}>
          {/* Background gray line */}
          <View style={[styles.progressBarBg, { width: lineWidth }]} />
          {/* Animated blue line */}
          <Animated.View
            style={[
              styles.progressBarFg,
              {
                width: loadingAnim.interpolate({
                  inputRange: [0, 1],
                  outputRange: [0, lineWidth],
                }),
              },
            ]}
          />
        </View>
      </Animated.View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#fff',
  },
  centered: {
    alignItems: 'center',
  },
  logoRow: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 20,
    gap: 20,
  },
  logo: {
    width: 80,
    height: 80,
    marginHorizontal: 10,
  },
  title: {
    fontSize: 26,
    fontWeight: 'bold',
    color: '#1e3a8a',
    marginBottom: 8,
  },
  subtitle: {
    fontSize: 18,
    color: '#6b7280',
    marginBottom: 32,
  },
  progressBarContainer: {
    marginTop: 20,
    width: '100%',
    alignItems: 'center',
    position: 'relative',
  },
  progressBarBg: {
    height: 3,
    backgroundColor: '#e5e7eb',
    borderRadius: 2,
  },
  progressBarFg: {
    height: 3,
    backgroundColor: '#3b82f6',
    borderRadius: 2,
    position: 'absolute',
    left: 0,
    top: 0,
  },
});