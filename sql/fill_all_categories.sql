-- Add missing quizzes and questions so every subject, difficulty, and question type has content.

INSERT IGNORE INTO quizzes (id, subject_id, title, difficulty, time_limit, total_marks, is_active) VALUES
(6, 2, 'Algebra Essentials', 'medium', 600, 6, 1),
(7, 2, 'Calculus Challenge', 'high', 900, 6, 1),
(8, 3, 'Everyday Facts', 'low', 300, 6, 1),
(9, 3, 'Global Mastery', 'high', 900, 6, 1),
(10, 4, 'Physics Basics', 'low', 300, 6, 1),
(11, 4, 'Motion and Energy', 'medium', 600, 6, 1),
(12, 4, 'Modern Physics', 'high', 900, 6, 1),
(13, 5, 'Grammar Basics', 'low', 300, 6, 1),
(14, 5, 'Reading Skills', 'medium', 600, 6, 1),
(15, 5, 'Advanced English', 'high', 900, 6, 1);

INSERT INTO questions (quiz_id, question_text, question_type, explanation, option_a, option_b, option_c, option_d, correct_option, marks) VALUES
-- Computer Science / low (add tf + short)
(1,'Python is an interpreted language.','tf','Python code is generally executed by an interpreter.','True','False','Both','None','a',1),
(1,'A variable can store changing data in a program.','tf','Variables are used to hold values that may change.','True','False','Maybe','Unknown','a',1),
(1,'Which keyword defines a function in Python?','short','Python functions are declared with the def keyword.','def','class','func','lambda','a',1),
(1,'What built-in function displays output on screen in Python?','short','The print() function displays output.','print','echo','show','display','a',1),

-- Computer Science / medium (add tf + short)
(2,'A stack follows the Last In, First Out rule.','tf','That is the definition of stack behavior.','True','False','Depends','Unknown','a',1),
(2,'A tree is a linear data structure.','tf','Trees are non-linear data structures.','True','False','Sometimes','Only in arrays','b',1),
(2,'Which data structure stores key-value pairs in many languages?','short','A hash map or dictionary stores key-value pairs.','Dictionary','Queue','Stack','Array','a',1),
(2,'What algorithmic technique repeatedly divides a problem into smaller parts?','short','Divide and conquer breaks a problem into smaller subproblems.','Divide and conquer','Backtracking','Brute force','Simulation','a',1),

-- Computer Science / high (add tf + short)
(3,'Dynamic programming solves problems by reusing answers to subproblems.','tf','Memoization or tabulation stores subproblem solutions.','True','False','Only for graphs','Only for recursion','a',1),
(3,'Depth-first search always finds the shortest path in an unweighted graph.','tf','BFS, not DFS, guarantees shortest path in an unweighted graph.','True','False','Sometimes','Only in trees','b',1),
(3,'Which notation describes the upper bound of algorithm growth?','short','Big O notation describes upper-bound growth.','Big O','Big Theta','Big Omega','Little o','a',1),
(3,'What data structure is typically used in Dijkstra to get the next smallest distance efficiently?','short','A priority queue or min-heap is commonly used.','Priority queue','Stack','Array','Linked list','a',1),

-- Mathematics / low (add tf + short)
(4,'An even number is divisible by 2.','tf','That is the definition of an even number.','True','False','Only for large numbers','Only for primes','a',1),
(4,'Zero is a positive number.','tf','Zero is neither positive nor negative.','True','False','Sometimes','Only in algebra','b',1),
(4,'What is 9 multiplied by 8?','short','9 times 8 equals 72.','72','64','81','78','a',1),
(4,'How many degrees are in a right angle?','short','A right angle measures 90 degrees.','90','45','180','60','a',1),

-- General Knowledge / medium (add tf + short)
(5,'The Pacific Ocean is the largest ocean on Earth.','tf','It covers more area than any other ocean.','True','False','Only by depth','Only in the south','a',1),
(5,'The Great Wall of China is in India.','tf','It is in China, not India.','True','False','Partly','Historically yes','b',1),
(5,'What is the capital city of Canada?','short','Ottawa is the capital of Canada.','Ottawa','Toronto','Vancouver','Montreal','a',1),
(5,'Which gas do plants absorb from the atmosphere?','short','Plants absorb carbon dioxide for photosynthesis.','Carbon dioxide','Oxygen','Nitrogen','Hydrogen','a',1),

-- Mathematics / medium
(6,'Solve: 2x + 5 = 15. What is x?','mcq','Subtract 5 then divide by 2, giving x = 5.','10','5','15','7','b',1),
(6,'What is the value of pi rounded to two decimal places?','mcq','Pi rounded to two decimals is 3.14.','3.12','3.14','3.16','3.18','b',1),
(6,'The sum of the angles in a triangle is 180 degrees.','tf','This is a basic property of Euclidean triangles.','True','False','Only in right triangles','Only in scalene triangles','a',1),
(6,'A fraction with denominator zero is defined.','tf','Division by zero is undefined.','True','False','Only in algebra','Only in geometry','b',1),
(6,'What is the formula for the area of a rectangle?','short','Area of a rectangle is length times width.','length x width','2 x length + 2 x width','base + height','length x length','a',1),
(6,'What is 12 squared?','short','12 squared equals 144.','144','122','24','154','a',1),

-- Mathematics / high
(7,'What is the derivative of x^2?','mcq','The derivative of x squared is 2x.','x','2x','x^2','2','b',1),
(7,'What is the integral of 1/x?','mcq','The antiderivative of 1/x is ln|x| + C.','x','1/x^2','ln|x| + C','e^x','c',1),
(7,'The derivative of a constant is zero.','tf','Constants do not change, so their derivative is zero.','True','False','Only for positive constants','Only for integers','a',1),
(7,'The determinant can be calculated only for non-square matrices.','tf','Determinants are defined for square matrices.','True','False','Sometimes','Only in calculus','b',1),
(7,'What is the quadratic formula term under the square root called?','short','b squared minus 4ac is called the discriminant.','Discriminant','Coefficient','Gradient','Factor','a',1),
(7,'What branch of math studies rates of change?','short','Calculus studies rates of change.','Calculus','Geometry','Arithmetic','Statistics','a',1),

-- General Knowledge / low
(8,'Which planet is known as the Red Planet?','mcq','Mars is often called the Red Planet.','Earth','Mars','Jupiter','Venus','b',1),
(8,'How many days are there in a week?','mcq','A week has seven days.','5','6','7','8','c',1),
(8,'Water freezes at 0 degrees Celsius.','tf','That is the standard freezing point of water.','True','False','Only in winter','Only in a freezer','a',1),
(8,'The sun rises in the west.','tf','The sun rises in the east.','True','False','Sometimes','Depends on country','b',1),
(8,'What color do you get by mixing red and white?','short','Red mixed with white makes pink.','Pink','Purple','Orange','Brown','a',1),
(8,'Which animal is known as the king of the jungle?','short','The lion is commonly called the king of the jungle.','Lion','Tiger','Elephant','Bear','a',1),

-- General Knowledge / high
(9,'Which layer of Earth lies between the crust and the core?','mcq','The mantle lies between the crust and the core.','Mantle','Troposphere','Outer shell','Basement','a',1),
(9,'Who wrote the play Hamlet?','mcq','Hamlet was written by William Shakespeare.','Charles Dickens','William Shakespeare','George Orwell','Leo Tolstoy','b',1),
(9,'The chemical symbol for gold is Au.','tf','Au is the symbol for gold from the Latin aurum.','True','False','Only in chemistry books','Only in Europe','a',1),
(9,'Light travels faster in vacuum than in water.','tf','Light travels fastest in vacuum.','True','False','Only at night','Only in labs','a',1),
(9,'What is the hardest natural substance on Earth?','short','Diamond is the hardest natural substance.','Diamond','Iron','Quartz','Granite','a',1),
(9,'Which organ pumps blood through the human body?','short','The heart pumps blood through the body.','Heart','Lung','Liver','Brain','a',1),

-- Physics / low
(10,'What force pulls objects toward Earth?','mcq','Gravity pulls objects toward Earth.','Magnetism','Gravity','Friction','Electricity','b',1),
(10,'What is the SI unit of force?','mcq','Force is measured in newtons.','Joule','Pascal','Newton','Watt','c',1),
(10,'An object at rest stays at rest unless acted on by a force.','tf','This is Newton''s first law of motion.','True','False','Only in space','Only for heavy objects','a',1),
(10,'Sound can travel through vacuum.','tf','Sound needs a medium, so it cannot travel in vacuum.','True','False','Only at low frequency','Only near stars','b',1),
(10,'What instrument is used to measure temperature?','short','A thermometer measures temperature.','Thermometer','Barometer','Ammeter','Voltmeter','a',1),
(10,'What is the speed unit commonly used for vehicles?','short','Speed is often measured in kilometers per hour.','Kilometers per hour','Newton','Celsius','Volt','a',1),

-- Physics / medium
(11,'Which type of energy is stored in a stretched spring?','mcq','A stretched spring stores elastic potential energy.','Thermal','Elastic potential','Sound','Nuclear','b',1),
(11,'What is the formula for speed?','mcq','Speed is distance divided by time.','time / distance','distance / time','force / area','mass / volume','b',1),
(11,'Acceleration is the rate of change of velocity.','tf','That is the definition of acceleration.','True','False','Only for cars','Only for free fall','a',1),
(11,'Power is measured in joules.','tf','Power is measured in watts; energy is measured in joules.','True','False','Only in chemistry','Only in mechanics','b',1),
(11,'What device converts electrical energy into light in a simple circuit?','short','A bulb or lamp converts electrical energy into light.','Bulb','Battery','Switch','Wire','a',1),
(11,'Which law states that every action has an equal and opposite reaction?','short','This is Newton''s third law.','Newton third law','Ohm law','Hooke law','Boyle law','a',1),

-- Physics / high
(12,'What particle carries a negative electric charge?','mcq','Electrons carry negative charge.','Proton','Neutron','Electron','Photon','c',1),
(12,'Which equation expresses energy-mass equivalence?','mcq','Einstein''s famous equation is E = mc^2.','F = ma','V = IR','E = mc^2','P = IV','c',1),
(12,'The speed of light in vacuum is constant.','tf','Modern physics treats c as a universal constant.','True','False','Only in experiments','Only in space','a',1),
(12,'Entropy always describes the speed of motion.','tf','Entropy is related to disorder and energy distribution, not speed.','True','False','Only in gases','Only in solids','b',1),
(12,'What branch of physics studies atoms and subatomic particles?','short','Quantum physics studies atomic and subatomic behavior.','Quantum physics','Optics','Mechanics','Thermodynamics','a',1),
(12,'What quantity is measured in ohms?','short','Electrical resistance is measured in ohms.','Resistance','Current','Voltage','Power','a',1),

-- English / low
(13,'Which word is a noun?','mcq','Teacher names a person, so it is a noun.','Run','Blue','Teacher','Quickly','c',1),
(13,'Choose the correct article: __ apple','mcq','We use an before a vowel sound.','a','an','the','no article','b',1),
(13,'A sentence begins with a capital letter.','tf','Standard English sentences begin with a capital letter.','True','False','Only in stories','Only in exams','a',1),
(13,'A verb names a person, place, or thing.','tf','A noun does that, not a verb.','True','False','Sometimes','Only in poetry','b',1),
(13,'What punctuation mark is used at the end of a question?','short','A question mark ends a direct question.','Question mark','Comma','Period','Colon','a',1),
(13,'What part of speech describes an action?','short','A verb describes an action or state.','Verb','Noun','Adjective','Article','a',1),

-- English / medium
(14,'Which sentence is in the past tense?','mcq','Yesterday, I walked to school is past tense.','I walk to school','I am walking to school','I will walk to school','Yesterday, I walked to school','d',1),
(14,'Choose the synonym of happy.','mcq','Glad is a synonym of happy.','Sad','Glad','Angry','Slow','b',1),
(14,'An adjective describes a noun.','tf','Adjectives modify nouns and pronouns.','True','False','Only in novels','Only in formal writing','a',1),
(14,'A paragraph should contain completely unrelated ideas.','tf','A paragraph should stay focused on one main idea.','True','False','Only in fiction','Only in reports','b',1),
(14,'What is the opposite of begin?','short','The opposite of begin is end.','End','Start','Open','Launch','a',1),
(14,'What do we call the main idea of a passage?','short','The central thought is the main idea.','Main idea','Heading','Caption','Footnote','a',1),

-- English / high
(15,'Which literary device compares two things using like or as?','mcq','A simile compares using like or as.','Metaphor','Simile','Irony','Hyperbole','b',1),
(15,'Choose the correctly punctuated sentence.','mcq','The sentence with correct punctuation is the standard form.','Lets eat, Grandma.','Let''s eat Grandma.','Lets eat Grandma.','Let''s eat, Grandma.','d',1),
(15,'A thesis statement presents the main argument of an essay.','tf','A thesis states the writer''s main claim.','True','False','Only in stories','Only in poems','a',1),
(15,'All persuasive writing avoids evidence.','tf','Persuasive writing relies on reasons and evidence.','True','False','Only in speeches','Only online','b',1),
(15,'What is a word with the opposite meaning called?','short','A word with the opposite meaning is an antonym.','Antonym','Synonym','Homonym','Acronym','a',1),
(15,'What is the term for the attitude of the writer toward the subject?','short','That attitude is called tone.','Tone','Plot','Theme','Setting','a',1);
