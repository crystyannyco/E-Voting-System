import { Tabs } from "expo-router";
import { Ionicons } from '@expo/vector-icons';

export default function TabsLayout() {
  return (
    <Tabs>
      <Tabs.Screen
        name="home"
        options={{
          title: "CSPC E-Voting",
          tabBarIcon: ({ color, size }) => (
            <Ionicons name="home-outline" size={size} color={color} />
          ),
        }}
      />
      <Tabs.Screen
        name="votes"
        options={{
          title: "Votes",
          tabBarIcon: ({ color, size }) => (
            <Ionicons name="list-outline" size={size} color={color} />
          ),
        }}
      />
      <Tabs.Screen
        name="profile"
        options={{
          title: "Profile",
          tabBarIcon: ({ color, size }) => (
            <Ionicons name="person-outline" size={size} color={color} />
          ),
        }}
      />
      <Tabs.Screen
        name="candidate-info"
        options={{
          href: null,
          title: "Candidate Information",
          tabBarIcon: ({ color, size }) => (
            <Ionicons name="person-outline" size={size} color={color} />
          ),
        }}
      />
      <Tabs.Screen
        name="cast-vote"
        options={{
          href: null,
          title: "Vote",
          tabBarIcon: ({ color, size }) => (
            <Ionicons name="person-outline" size={size} color={color} />
          ),
        }}
      />
      {/* <Tabs.Screen
        name="edit-profile"
        options={{
          href: null,
          title: "Edit Profile",
          tabBarIcon: ({ color, size }) => (
            <Ionicons name="person-outline" size={size} color={color} />
          ),
        }}
      /> */}
    </Tabs>
  );
}
