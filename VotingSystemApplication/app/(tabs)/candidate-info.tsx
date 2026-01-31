import React from 'react';
import { View, Text, StyleSheet, ScrollView, Image, RefreshControl } from 'react-native';
import { useLocalSearchParams } from 'expo-router';
import { useEffect, useState } from 'react';
import { ActivityIndicator } from 'react-native';

const HEADER_MAX_HEIGHT = 260;

export default function CandidateInfo() {
  const { id } = useLocalSearchParams();
  const [candidate, setCandidate] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchCandidate = async () => {
    try {
      const res = await fetch(`http://172.16.116.113:8080/api/candidate/${id}`);
      const data = await res.json();
      setCandidate(data.data);
    } catch (err) {
      setCandidate(null);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    if (!id) return;
    setLoading(true);
    fetchCandidate();
  }, [id]);

  const handleRefresh = () => {
    setRefreshing(true);
    fetchCandidate();
  };

  if (loading) {
    return <ActivityIndicator size="large" color="#3B82F6" style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }} />;
  }

  if (!candidate) {
    return (
      <ScrollView
        contentContainerStyle={styles.container}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={handleRefresh} colors={["#3B82F6"]} />}
      >
        <Text style={styles.text}>Candidate not found.</Text>
      </ScrollView>
    );
  }

  return (
    <View style={styles.containerAlt}>
      <View
        style={[
          styles.headerImageContainer,
          {
            position: 'absolute',
            top: 0,
            left: 0,
            right: 0,
            height: HEADER_MAX_HEIGHT,
            zIndex: 1
          },
        ]}
      >
        <Image
          source={candidate.ProfileUrl ? { uri: candidate.ProfileUrl } : require('../../assets/images/logos/cspc-logo.png')}
          style={styles.fullHeaderImageNoCircle}
        />
      </View>
      <ScrollView
        style={{ flex: 1, width: '100%' }}
        contentContainerStyle={{ paddingTop: HEADER_MAX_HEIGHT }}
        showsVerticalScrollIndicator={false}
        bounces={false}
        overScrollMode="never"
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={handleRefresh} colors={["#3B82F6"]} />
        }
      >
        <View style={[styles.infoCard, { zIndex: 2 }]}>
          <Text style={styles.candidateName}>
            {candidate.FirstName} {candidate.MiddleName} {candidate.LastName}
          </Text>
          <Text style={styles.position}>{candidate.PositionName}</Text>
          <Text style={styles.sectionTitle}>Partylist</Text>
          <Text style={styles.partylist}>{candidate.PartylistName || 'Independent'}</Text>
          <Text style={styles.sectionTitle}>Election</Text>
          <Text style={styles.election}>{candidate.ElectionName || 'N/A'}</Text>
          <Text style={styles.platformLabel}>Platform</Text>
          <View style={styles.platformBox}>
            <Text style={styles.platformText}>{candidate.Platform || 'No platform provided.'}</Text>
          </View>
          <View style={{ flex: 1 }} />
          {/* <View style={styles.voteBtnContainer}>
            <Text style={styles.voteBtn}>Vote</Text>
          </View> */}
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f3f4f6',
    padding: 16,
  },
  containerAlt: {
    flex: 1,
    backgroundColor: '#e9f0f8',
  },
  headerRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 48,
    marginLeft: 24,
    marginBottom: 24,
  },
  headerRowCentered: {
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 48,
    marginBottom: 24,
    width: '100%',
  },
  headerImageContainer: {
    width: '100%',
    height: 260,
    backgroundColor: '#e9f0f8',
    alignItems: 'center',
    justifyContent: 'center',
    overflow: 'hidden',
  },
  closeBtn: {
    fontSize: 36,
    color: '#333',
  },
  headerProfileImage: {
    width: 80,
    height: 80,
    borderRadius: 40,
    marginRight: 12,
    borderWidth: 2,
    borderColor: '#3b82f6',
    backgroundColor: '#e5e7eb',
  },
  fullHeaderImage: {
    width: 160,
    height: 160,
    borderRadius: 80,
    borderWidth: 3,
    borderColor: '#3b82f6',
    backgroundColor: '#e5e7eb',
  },
  fullHeaderImageNoCircle: {
    width: '100%',
    height: '100%',
    resizeMode: 'cover',
    borderRadius: 0,
  },
  infoCard: {
    flex: 1,
    backgroundColor: '#fff',
    borderTopLeftRadius: 0,
    borderTopRightRadius: 0,
    padding: 32,
    paddingTop: 20,
    alignItems: 'flex-start',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: -2 },
    shadowOpacity: 0.06,
    shadowRadius: 8,
    elevation: 8,
  },
  candidateName: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#1E40B7',
    marginBottom: 0,
  },
  position: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#222',
    marginBottom: 20,
  },
  platformLabel: {
    marginTop: 10,
    fontSize: 16,
    fontWeight: 'bold',
    color: '#3b82f6',
    marginBottom: 4,
    textAlign: 'left',
  },
  platformBox: {
    width: '100%',
    backgroundColor: '#f1f5f9',
    borderRadius: 12,
    padding: 16,
    marginBottom: 18,
    minHeight: 60,
  },
  platformText: {
    fontSize: 15,
    color: '#334155',
    lineHeight: 22,
  },
  voteBtnContainer: {
    width: '100%',
    alignItems: 'center',
    marginTop: 6,
    marginBottom: 0,
  },
  voteBtn: {
    backgroundColor: '#1062fe',
    color: '#fff',
    fontSize: 20,
    fontWeight: 'bold',
    paddingVertical: 16,
    paddingHorizontal: 48,
    borderRadius: 16,
    overflow: 'hidden',
    textAlign: 'center',
    width: '100%',
  },
  card: {
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 24,
    alignItems: 'center',
    width: 340,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 8,
    elevation: 4,
  },
  profileImage: {
    width: 120,
    height: 120,
    borderRadius: 60,
    marginBottom: 16,
    borderWidth: 2,
    borderColor: '#3b82f6',
    backgroundColor: '#e5e7eb',
  },
  name: {
    fontSize: 22,
    fontWeight: 'bold' as const,
    color: '#1e293b',
    marginBottom: 4,
    textAlign: 'center' as const,
  },
  partylist: {
    fontSize: 15,
    color: '#64748b',
    marginBottom: 12,
    textAlign: 'center' as const,
  },
  section: {
    width: '100%',
    marginTop: 12,
    marginBottom: 4,
    paddingHorizontal: 8,
  },
  sectionTitle: {
    fontSize: 14,
    color: '#3b82f6',
    fontWeight: 'bold' as const,
    marginBottom: 2,
  },
  platform: {
    fontSize: 14,
    color: '#334155',
    textAlign: 'center' as const,
    marginBottom: 4,
  },
  election: {
    fontSize: 14,
    color: '#475569',
    textAlign: 'center' as const,
    marginBottom: 10,
  },
  text: {
    fontSize: 20,
    color: '#333',
  },
  partylistLabel: {
    fontSize: 16,
    color: '#888',
    marginBottom: 2,
    fontWeight: '600',
    marginTop: 8,
  },
  partylistText: {
    fontSize: 16,
    color: '#2563eb',
    marginBottom: 8,
    fontWeight: 'bold',
  },
  electionLabel: {
    fontSize: 16,
    color: '#888',
    marginBottom: 2,
    fontWeight: '600',
    marginTop: 8,
  },
  electionText: {
    fontSize: 16,
    color: '#334155',
    marginBottom: 12,
    fontWeight: 'bold',
  },
});
