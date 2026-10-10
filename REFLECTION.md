# Reflection

**1. Name two security risks in your app as it stands and how you would fix each.**
Ang unang risk ay walang authentication ang ating API, kaya kahit sino ay pwedeng mag-POST ng fake na water level readings at magdulot ng panic. Maaayos ito sa pamamagitan ng paglalagay ng API key na eksklusibo lamang sa totoong IoT sensor. Ang pangalawang risk ay ang kawalan ng rate limiting, kaya pwedeng ma-spam ang ating server; aayusin ko ito sa pamamagitan ng pag-limit ng requests bawat IP address.

**2. Who is responsible when the AI writes a wrong advisory, and what safeguard did you add?**
Ang developer at ang barangay ang may buong responsibilidad dito dahil ang AI ay isang tool lamang na sumusunod sa parameters na binigay natin. Bilang safeguard, naglagay ako ng rule-based fallback code na gagamitin kung sakaling pumalya ang API, at nilimitahan ko rin ang context na ibinibigay sa prompt base lamang sa totoong numero para maiwasan ang AI hallucinations.

**3. Pick one: AR/VR or blockchain. Describe one realistic way it could extend this app, and one reason it might not be worth the cost.**
Pwedeng gamitin ang blockchain para makagawa ng immutable ledger ng mga sensor readings at mag-set ng strict na role-based access control kung sino ang pwedeng magdagdag ng data nodes. Pero maaaring hindi ito sulit dahil masyadong malaki ang overhead at cost para sa isang simpleng barangay monitoring app na hindi naman nangangailangan ng matinding decentralized trust.

**4. Explain how big data analytics would change if the barangay installed 200 sensors across Metro Manila instead of one.**
Kung may 200 sensors, hindi na uubra ang simpleng pagkuha ng average. Kakailanganin na nating gumamit ng deep neural networks at machine learning algorithms para sa predictive maintenance ng napakaraming sensors at para makapag-train ng models na makaka-detect ng complex city-wide flooding patterns bago pa man tuluyang tumaas ang tubig.
