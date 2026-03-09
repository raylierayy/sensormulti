from anthropic import Agent

agent = Agent()

def run_frontend_interview():
    # Architecture & Frameworks
    answer = agent.ask_user_question("Which front-end framework or approach are you using (plain HTML/CSS/JS, Bootstrap, React, etc.), and why?")
    print("User answered:", answer)

    # Layout & Dashboard
    answer = agent.ask_user_question("How do you plan to structure the dashboard so multiple sensor readings are clear without overwhelming the user?")
    print("User answered:", answer)

    # Visual Design
    answer = agent.ask_user_question("What color coding or visual cues will you use to indicate sensor states like active, error, or offline?")
    print("User answered:", answer)

    # Responsiveness
    answer = agent.ask_user_question("Have you considered mobile responsiveness? How will the interface adapt to smaller screens?")
    print("User answered:", answer)

    # Accessibility
    answer = agent.ask_user_question("What accessibility features (contrast, keyboard navigation, screen reader support) are you planning to include?")
    print("User answered:", answer)

    # Performance
    answer = agent.ask_user_question("How will you optimize front-end performance when sensor data updates frequently?")
    print("User answered:", answer)

    # Tradeoffs
    answer = agent.ask_user_question("Did you consider separating the front-end from the PHP backend (e.g., using an API + modern JS framework)? What tradeoffs influenced your choice?")
    print("User answered:", answer)
