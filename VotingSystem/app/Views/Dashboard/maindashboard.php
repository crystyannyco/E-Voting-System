<!-- Dashboard Content -->
<div class="p-6">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">Dashboard</h3>
    
    <!-- SECTION: Stats Grid with Updated Colors and Icons -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- <h3 class="text-xl font-semibold text-gray-800 mb-4">Election Analysis</h3> -->
        <!-- Total Voters Card -->
        <a href="/student">
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Voters</p>
                    <h4 class="text-2xl font-bold"><?= $totalStudents ?></h4>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                    </svg>
                </div>
            </div>
        </a>
        
        <!-- Total Elections Card - Updated Color and Icon -->
        <a href="/election">
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">No. of Elections</p>
                    <h4 class="text-2xl font-bold"><?= $totalElections ?></h4>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                        <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                    </svg>
                </div>
            </div>
        </a>

        <!-- No. of candidate Card - Updated Color and Icon -->
        <a href="/candidate">
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">No. of Candidate</p>
                    <h4 class="text-2xl font-bold"><?= $totalCandidates ?></h4>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                    <span class="material-icons">person</span>
                </div>
            </div>
        </a>
        
        <!-- No. of position Card - Updated Color and Icon -->
        <a href="/partylist">
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">No. of Partylist</p>
                    <h4 class="text-2xl font-bold"><?= $totalPartylists ?></h4>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"></path>
                    </svg>
                </div>
            </div>
        </a>
    </div>
    
    <!-- SECTION: Analysis Section -->
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Election Analysis</h3>
        <?php if (empty($elections)): ?>
            <p class="text-gray-500 text-center py-4">No elections found.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($elections as $election): ?>
                    <a href="/votes?election_id=<?= $election['ElectionID'] ?>" data-title="<?= htmlspecialchars($election['ElectionName']) ?>">
                        <div class="bg-white rounded-lg shadow-md p-6">
                        <?php 
                            $stats = $electionStats[$election['ElectionID']]; 
                            $votedPercentage = $stats['votedPercentage'];
                            $notVotedPercentage = 100 - $votedPercentage;
                        ?>
                        <p class="text-xl font-semibold text-blue-800 mb-1">
                            <?= $election['ElectionName'] ?>
                            <?php if ($election['Department'] == 0): ?>
                                <span class="text-sm text-gray-600">(All Departments)</span>
                            <?php else: ?>
                                <span class="text-sm text-gray-600">(<?= $departments[$election['Department']] ?? 'Department '.$election['Department'] ?>)</span>
                            <?php endif; ?>
                        </p>
                        <div class="relative pt-1 mb-4">
                            <div class="flex rounded-lg h-6 overflow-hidden text-white">
                                <div class="bg-blue-600 flex items-center justify-center text-sm font-bold" style="width: <?= $votedPercentage ?>%">
                                    <?= $votedPercentage ?>%
                                </div>
                                <div class="bg-red-600 flex items-center justify-center text-sm font-bold" style="width: <?= $notVotedPercentage ?>%">
                                    <?= $notVotedPercentage ?>%
                                </div>
                            </div>
                        </div>
                        
                        <!-- Voting Legend -->
                        <div class="flex flex-col sm:flex-row justify-between px-4">
                            <div class="flex items-center mb-2 sm:mb-0">
                                <div class="w-4 h-4 bg-blue-600 rounded-full mr-2"></div>
                                <span class="text-gray-800 font-medium">ALREADY VOTED: <?= $stats['votedStudents'] ?> students</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-red-600 rounded-full mr-2"></div>
                                <span class="text-gray-800 font-medium">NOT YET VOTED: <?= $stats['eligibleStudents'] - $stats['votedStudents'] ?> students</span>
                            </div>
                        </div>
                        
                        <?php if ($election['Department'] == 0 && !empty($stats['departmentBreakdown'])): ?>
                            <div class="mt-4 pt-3 border-t border-gray-200">
                                <p class="text-sm font-medium text-gray-700 mb-2">Department Breakdown:</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    <?php foreach ($stats['departmentBreakdown'] as $deptId => $deptStats): ?>
                                        <?php if (isset($departments[$deptId])): ?>
                                        <div class="bg-gray-50 p-2 rounded">
                                            <p class="text-xs font-medium text-gray-600"><?= $departments[$deptId] ?></p>
                                            <div class="flex justify-between text-xs">
                                                <span>Voted: <?= $deptStats['votedPercentage'] ?>%</span>
                                                <span>(<?= $deptStats['votedStudents'] ?>/<?= $deptStats['totalStudents'] ?>)</span>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- SECTION: candidate Section (Restructured for one position per line) -->
    <div>
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Candidates</h3>
        
        <?php foreach ($positions as $positionId => $positionName): ?> 
            <?php if (isset($candidatesByPosition[$positionId])): ?>
                <a href="/candidate">
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 text-center"><?= $positionName ?></h4>
                        
                        <div class="flex justify-center"> 
                            <div class="flex flex-wrap justify-center gap-4 max-w-screen-xl">
                                <?php foreach ($candidatesByPosition[$positionId] as $candidate): ?>
                                    <div class="bg-blue-50 rounded-lg p-4 flex flex-col items-center w-72">
                                        <div class="flex justify-center mb-4 w-full h-64">
                                            <div class="w-64 h-64 rounded overflow-hidden flex items-center justify-center bg-white">
                                                <?php if (!empty($candidate['Profile']) && is_string($candidate['Profile'])): ?>
                                                    <img src="<?= base_url('uploads/profiles/' . $candidate['Profile']) ?>" 
                                                        alt="<?= $candidate['FirstName'] ?>" 
                                                        class="object-contain max-w-full ">
                                                <?php else: ?>
                                                    <img src="<?= base_url('assets/default-profile.png') ?>" 
                                                        alt="Default Profile" 
                                                        class="object-contain max-w-full ">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <h5 class="font-medium text-gray-800 text-center">
                                            <?= $candidate['FirstName'] . ' ' . $candidate['LastName'] ?>
                                        </h5>
                                        <p class="text-sm text-gray-500 text-center">
                                            <?= $candidate['PartylistName'] ?? 'Independent' ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('a[href="/candidate"]').forEach(link => {
            link.addEventListener('click', function() {
                sessionStorage.setItem('pageTitle', 'Candidates');
            });
        });

        // Set topbar title to election name when clicking an election
        document.querySelectorAll('a[href^="/votes"][data-title]').forEach(link => {
            link.addEventListener('click', function() {
                sessionStorage.setItem('pageTitle', this.getAttribute('data-title'));
            });
        });
    });
</script>