import React, { useEffect, useState, useCallback } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  ScrollView, 
  TouchableOpacity, 
  Image,
  ActivityIndicator,
  FlatList,
  RefreshControl
} from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { router } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

interface Student {
  FirstName: string;
  LastName: string;
  StudentID: string;
  Email: string;
  Gender: string;
  Year?: string;
  Department?: string;
  Course?: string;
}

interface TurnoutData {
  eligibleStudents: number;
  votedStudents: number;
  notVotedStudents: number;
  votedPercentage: number;
  notVotedPercentage: number;
  departmentId?: number;
}

export default function HomeScreen() {
  const [student, setStudent] = useState<Student | null>(null);
  const [loading, setLoading] = useState(true);
  const [elections, setElections] = useState<any[]>([]);
  const [candidates, setCandidates] = useState<any[]>([]);
  const [departmentElections, setDepartmentElections] = useState<any[]>([]);
  const [universityElections, setUniversityElections] = useState<any[]>([]);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    const checkAuth = async () => {
      try {
        const userData = await AsyncStorage.getItem('userData');
        if (!userData) {
          router.replace('/login');
          return;
        }
        setStudent(JSON.parse(userData));
      } catch (error) {
        console.error('Error fetching user data:', error);
      } finally {
        setLoading(false);
      }
    };
    checkAuth();
  }, []);

  const fetchElectionAndTurnout = async () => {
    try {
      const res = await fetch('http://172.16.116.113:8080/api/elections');
      const data = await res.json();
      if (data && data.data && student) {
        // All university-wide elections (Department == 0)
        const uniElections = data.data.filter((e: any) => e.election.Department == 0).map((uniElection: any) => ({
          id: uniElection.election.ElectionID,
          title: uniElection.election.ElectionName,
          rawStart: uniElection.election.Start,
          rawEnd: uniElection.election.End,
          date: `${uniElection.election.Start} - ${uniElection.election.End}`,
          status: 'Active',
          department: uniElection.departmentName,
          turnout: uniElection.turnout
        }));
        setUniversityElections(uniElections);

        // All department elections for student's department
        const deptElections = data.data.filter((e: any) => e.election.Department == student.Department).map((deptElection: any) => ({
          id: deptElection.election.ElectionID,
          title: deptElection.election.ElectionName,
          rawStart: deptElection.election.Start,
          rawEnd: deptElection.election.End,
          date: `${deptElection.election.Start} - ${deptElection.election.End}`,
          status: 'Active',
          department: deptElection.departmentName,
          turnout: deptElection.turnout
        }));
        setDepartmentElections(deptElections);
      } else {
        setUniversityElections([]);
        setDepartmentElections([]);
      }
    } catch (err) {
      console.error('Failed to fetch election info:', err);
      setUniversityElections([]);
      setDepartmentElections([]);
    }
  };

  const fetchCandidates = async () => {
    try {
      const res = await fetch('http://172.16.116.113:8080/api/candidates');
      const data = await res.json();
      if (data && data.data) {
        setCandidates(data.data);
      }
    } catch (err) {
      console.error('Failed to fetch candidates:', err);
    }
  };

  useEffect(() => {
    if (student) fetchElectionAndTurnout();
  }, [student]);

  useEffect(() => {
    fetchCandidates();
  }, []);

  const onRefresh = useCallback(async () => {
    setRefreshing(true);
    await fetchElectionAndTurnout();
    await fetchCandidates();
    setRefreshing(false);
  }, [student]);

  // Helper to get election status
  function getElectionStatus(start: string, end: string): { label: string; color: string } {
    const now = new Date();
    const startDate = new Date(start);
    const endDate = new Date(end);
    if (now < startDate) return { label: 'Coming Soon', color: '#F59E42' };
    if (now > endDate) return { label: 'Closed', color: '#DC2626' };
    return { label: 'Ongoing', color: '#16A34A' };
  }

  // Helper to format date range
  function formatDateRange(start: string, end: string): string {
    const startDate = new Date(start);
    const endDate = new Date(end);
    const options: Intl.DateTimeFormatOptions = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return `${startDate.toLocaleString(undefined, options)} - ${endDate.toLocaleString(undefined, options)}`;
  }

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#3B82F6" />
      </View>
    );
  }

  return (
    <ScrollView
      style={styles.container}
      contentContainerStyle={{ paddingBottom: 0 }}
      refreshControl={
        <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
      }
    >
      {/* Welcome Section */}
      <View style={styles.welcomeSection}>
        <View style={styles.welcomeContent}>
          <Text style={styles.welcomeText}>Welcome back,</Text>
          <Text style={styles.nameText}>{student?.FirstName || 'Student'}</Text>
          <Text style={styles.subtitleText}>Your vote is your voice!</Text>
        </View>
        <Image 
          source={require('../../assets/images/logos/cspc-logo.png')}
          style={styles.logoImage}
        />
      </View>
      {/* Elections Section - Horizontal Scroll */}
      <View style={{ marginTop: 20, marginBottom: 8 }}>
        <Text style={{ fontSize: 20, fontWeight: 'bold', color: '#1E293B', marginLeft: 24, marginBottom: 12 }}>Elections</Text>
        {universityElections.length === 0 && departmentElections.length === 0 ? (
          <Text style={{ color: '#64748B', fontSize: 16, textAlign: 'center', marginTop: 24 }}>No elections available.</Text>
        ) : (
          <FlatList
            data={[...universityElections, ...departmentElections]}
            keyExtractor={(item) => item.id.toString()}
            horizontal
            showsHorizontalScrollIndicator={false}
            contentContainerStyle={{ paddingLeft: 16, paddingRight: 8 }}
            renderItem={({ item }) => (
              <View style={{
                width: 280,
                backgroundColor: '#FFFFFF',
                borderRadius: 12,
                marginRight: 16,
                marginBottom: 4,
                padding: 0,
                shadowColor: '#000',
                shadowOpacity: 0.08,
                shadowRadius: 4,
                elevation: 2,
                borderWidth: 1,
                borderColor: '#E5E7EB',
                alignSelf: 'flex-start',
              }}>
                <View style={{
                  flexDirection: 'row',
                  justifyContent: 'space-between',
                  alignItems: 'center',
                  paddingHorizontal: 18,
                  paddingTop: 16,
                  paddingBottom: 6,
                }}>
                  <Text style={{ fontSize: 17, fontWeight: 'bold', color: '#1E293B' }}>
                    {item.department === 'All Departments' ? 'University Election' : 'Department Election'}
                  </Text>
                </View>
                <View style={{ paddingHorizontal: 18, paddingBottom: 12 }}>
                  <View style={{ marginBottom: 8 }}>
                    <Text style={{ fontWeight: 'bold', fontSize: 16, color: '#334155', marginBottom: 2 }}>{item.title}</Text>
                    <Text style={{ fontSize: 12, color: '#64748B', marginBottom: 2 }}>{formatDateRange(item.rawStart, item.rawEnd)}</Text>
                    <Text style={{ color: '#64748B', fontSize: 13, marginTop: 2 }}>{item.department}</Text>
                  </View>
                  {/* Status below all info */}
                  <View style={{ alignItems: 'stretch', marginTop: 8 }}>
                    {(() => {
                      const status = getElectionStatus(item.rawStart, item.rawEnd);
                      return (
                        <View style={{ backgroundColor: status.color + '22', paddingHorizontal: 16, paddingVertical: 6, borderRadius: 10, alignSelf: 'stretch', alignItems: 'center' }}>
                          <Text style={{ color: status.color, fontWeight: 'bold', fontSize: 14, textAlign: 'center' }}>{status.label}</Text>
                        </View>
                      );
                    })()}
                  </View>
                  {/* Turnout Bar */}
                  {item.turnout && (
                    <View style={{ backgroundColor: '#F1F5F9', padding: 12, borderRadius: 10, marginTop: 12 }}>
                      <Text style={{ fontWeight: 'bold', fontSize: 14, color: '#1E293B', marginBottom: 3, textAlign: 'left' }}>VOTES</Text>
                      <View style={{ flexDirection: 'row', height: 36, borderRadius: 8, overflow: 'hidden', backgroundColor: '#E5E7EB' }}>
                        <View style={{ flex: item.turnout.votedPercentage, backgroundColor: '#2563EB', justifyContent: 'center', alignItems: 'center' }}>
                          {item.turnout.votedPercentage > 0 ? (
                            <Text style={{ color: 'white', fontWeight: 'bold', fontSize: 14 }}>{item.turnout.votedPercentage}%</Text>
                          ) : null}
                        </View>
                        <View style={{ flex: item.turnout.notVotedPercentage, backgroundColor: '#DC2626', justifyContent: 'center', alignItems: 'center' }}>
                          {item.turnout.notVotedPercentage > 0 ? (
                            <Text style={{ color: 'white', fontWeight: 'bold', fontSize: 14 }}>{item.turnout.notVotedPercentage}%</Text>
                          ) : null}
                        </View>
                      </View>
                      <View style={{ flexDirection: 'row', marginTop: 10, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flexDirection: 'row', alignItems: 'center', marginRight: 16 }}>
                          <View style={{ width: 10, height: 10, borderRadius: 5, backgroundColor: '#2563EB', marginRight: 6 }} />
                          <Text style={{ fontSize: 11, color: '#1E293B' }}>ALREADY VOTED</Text>
                        </View>
                        <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                          <View style={{ width: 10, height: 10, borderRadius: 5, backgroundColor: '#DC2626', marginRight: 6 }} />
                          <Text style={{ fontSize: 11, color: '#1E293B' }}>NOT YET VOTED</Text>
                        </View>
                      </View>
                    </View>
                  )}
                </View>
              </View>
            )}
          />
        )}
      </View>
      {/* Candidates Section */}
      <View style={{ marginTop: 20, marginBottom: 0 }}>
        <Text style={{ fontSize: 20, fontWeight: 'bold', color: '#1E293B', marginLeft: 16, marginBottom: 12 }}>All Candidates</Text>
        {/* Group candidates by election */}
        {(() => {
          // Group candidates by ElectionName
          const grouped = candidates.reduce((acc: Record<string, any[]>, candidate) => {
            const election = candidate.ElectionName || 'Other';
            if (!acc[election]) acc[election] = [];
            acc[election].push(candidate);
            return acc;
          }, {} as Record<string, any[]>);
          return Object.entries(grouped).map(([electionName, group]) => (
            <View key={electionName} style={{
              marginBottom: 20,
              backgroundColor: '#fff',
              borderRadius: 10,
              marginHorizontal: 16,
              shadowColor: '#000',
              shadowOpacity: 0.06,
              shadowRadius: 6,
              elevation: 1,
              borderWidth: 1,
              borderColor: '#E5E7EB',
            }}>
              <View style={{
                backgroundColor: '#1E3A8A',
                borderTopLeftRadius: 10,
                borderTopRightRadius: 10,
                paddingVertical: 12,
                paddingHorizontal: 12,
                marginBottom: 2,
                alignItems: 'center',
                justifyContent: 'center',
                shadowColor: '#2563EB',
                shadowOpacity: 0.08,
                shadowRadius: 4,
                elevation: 2,
              }}>
                <Text style={{
                  fontSize: 14,
                  fontWeight: 'bold',
                  color: '#fff',
                  letterSpacing: 0.5,
                  textAlign: 'center',
                  textShadowColor: 'rgba(0,0,0,0.08)',
                  textShadowOffset: { width: 0, height: 1 },
                  textShadowRadius: 2,
                }}>{electionName}</Text>
              </View>
              <FlatList
                data={group as any[]}
                keyExtractor={(item) => item.CandidateID.toString()}
                horizontal
                showsHorizontalScrollIndicator={false}
                contentContainerStyle={{ paddingLeft: 12, paddingRight: 6, paddingVertical: 12 }}
                renderItem={({ item }) => (
                  <TouchableOpacity
                    onPress={() => router.push({ pathname: '/(tabs)/candidate-info', params: { id: item.CandidateID } })}
                    activeOpacity={0.8}
                    style={{
                      width: 200,
                      backgroundColor: '#F8FAFC',
                      borderRadius: 16,
                      marginRight: 16,
                      padding: 0,
                      alignItems: 'center',
                      shadowColor: '#000',
                      shadowOpacity: 0.08,
                      shadowRadius: 4,
                      elevation: 2,
                      borderWidth: 1,
                      borderColor: '#E5E7EB',
                    }}
                  >
                    <View style={{
                      width: '100%',
                      height: 200,
                      backgroundColor: '#E0E7EF',
                      borderTopLeftRadius: 16,
                      borderTopRightRadius: 16,
                      justifyContent: 'center',
                      alignItems: 'center',
                      overflow: 'hidden',
                    }}>
                      <Image
                        source={item.ProfileUrl ? { uri: item.ProfileUrl } : require('../../assets/images/logos/cspc-logo.png')}
                        style={{ width: '100%', height: '100%', borderTopLeftRadius: 16, borderTopRightRadius: 16, borderBottomLeftRadius: 0, borderBottomRightRadius: 0, resizeMode: 'cover', backgroundColor: '#E5E7EB' }}
                      />
                    </View>
                    <View style={{ padding: 14, alignItems: 'center', width: '100%' }}>
                      <Text style={{ fontWeight: 'bold', fontSize: 15, color: '#1E293B', textAlign: 'center', marginBottom: -2 }}>
                        {item.FirstName} {item.LastName}
                      </Text>
                      <Text style={{ color: '#64748B', fontSize: 13, textAlign: 'center', marginBottom: -2 }}>
                        {item.PartylistName || 'No Partylist'}
                      </Text>
                      <Text style={{ color: '#334155', fontSize: 13, textAlign: 'center', fontWeight: '600', marginBottom: 2 }}>
                        {item.PositionName || 'No Position'}
                      </Text>
                    </View>
                  </TouchableOpacity>
                )}
              />
            </View>
          ));
        })()}
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F1F5F9',
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#F1F5F9',
  },
  welcomeSection: {
    backgroundColor: '#1E40AF',
    padding: 20,
    paddingTop: 30,
    paddingBottom: 30,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  welcomeContent: {
    flex: 1,
  },
  welcomeText: {
    color: '#E0E7FF',
    fontSize: 16,
    fontFamily: 'Inter-Regular',
  },
  nameText: {
    color: '#FFFFFF',
    fontSize: 24,
    fontWeight: 'bold',
    marginTop: 4,
    fontFamily: 'Inter-Bold',
  },
  subtitleText: {
    color: '#93C5FD',
    fontSize: 14,
    marginTop: 4,
    fontFamily: 'Inter-Regular',
  },
  logoImage: {
    width: 60,
    height: 60,
    resizeMode: 'contain',
    marginRight: 10,
  },
  sectionContainer: {
    marginTop: 20,
    marginHorizontal: 16,
    backgroundColor: '#FFFFFF',
    borderRadius: 10,
    padding: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
    elevation: 2,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 10,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#1E293B',
    fontFamily: 'Inter-SemiBold',
  },
  viewAllText: {
    color: '#3B82F6',
    fontSize: 14,
    fontFamily: 'Inter-Medium',
  },
  electionCard: {
    backgroundColor: '#F8FAFC',
    borderRadius: 8,
    padding: 16,
    marginVertical: 8,
    borderLeftWidth: 4,
    borderLeftColor: '#3B82F6',
  },
  electionCardContent: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  electionTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: '#334155',
    fontFamily: 'Inter-SemiBold',
  },
  electionDate: {
    fontSize: 11,
    color: '#64748B',
    marginTop: 4,
    fontFamily: 'Inter-Regular',
  },
  statusBadge: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 20,
  },
  activeStatusBadge: {
    backgroundColor: '#DCF0E6',
  },
  statusText: {
    fontSize: 12,
    color: '#16A34A',
    fontFamily: 'Inter-Medium',
  },
  cardAction: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'flex-end',
    marginTop: 10,
  },
  actionText: {
    fontSize: 14,
    color: '#3B82F6',
    marginRight: 4,
    fontFamily: 'Inter-Medium',
  },
  quickLinksContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginTop: 10,
  },
  quickLinkCard: {
    backgroundColor: '#F8FAFC',
    borderRadius: 8,
    padding: 12,
    alignItems: 'center',
    width: '30%',
  },
  quickLinkIcon: {
    width: 50,
    height: 50,
    borderRadius: 25,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 8,
  },
  quickLinkText: {
    fontSize: 12,
    color: '#475569',
    textAlign: 'center',
    fontFamily: 'Inter-Medium',
  },
});