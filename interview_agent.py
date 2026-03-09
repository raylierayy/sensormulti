from anthropic import Agent

agent = Agent()

def run_interview():
    # Example: ask about technical design
    answer = agent.ask_user_question("How do you plan to handle multiple sensor inputs concurrently?")
    print("User answered:", answer)

    # Example: ask about UI/UX
    answer = agent.ask_user_question("What user experience concerns do you anticipate with the dashboard?")
    print("User answered:", answer)
