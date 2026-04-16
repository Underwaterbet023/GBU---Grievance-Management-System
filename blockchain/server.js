const express = require("express");
const cors = require("cors");
const app = express();
app.use(express.json());
app.use(cors());

const Blockchain = require("./blockchain");
let chain = new Blockchain();

app.post("/add", (req, res) => {
  chain.addBlock({
    index: chain.chain.length,
    timestamp: Date.now(),
    data: req.body
  });
  console.log("Block added:", chain.getLatestBlock());
  res.json({ message: "Block added", block: chain.getLatestBlock() });
});

app.get("/chain", (req, res) => {
    res.json(chain);
});

const PORT = 3000;
app.listen(PORT, () => console.log(`Blockchain running on port ${PORT}`));
